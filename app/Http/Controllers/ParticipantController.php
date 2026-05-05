<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Campagne;
use App\Models\CoursDEau;
use App\Models\SessionParticipant;
use Illuminate\Support\Facades\DB;

class ParticipantController extends Controller
{
    public function showJoin()
    {
        if (session()->has('participant')) {
            return redirect()->route('participant.analyses');
        }
        return view('participant.join');
    }

    public function validateCode(\Illuminate\Http\Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $campagne = Campagne::where('code', strtoupper(trim($request->code)))->first();

        if (! $campagne) {
            return response()->json(['error' => 'Code invalide. Vérifiez et réessayez.'], 404);
        }

        return response()->json([
            'campagne_id' => $campagne->id,
            'nom'         => $campagne->nom,
            'nb_groupes'  => $campagne->nb_groupes,
        ]);
    }

    public function register(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'campagne_id' => 'required|integer|exists:campagnes,id',
            'pseudo'      => 'required|string|max:100',
            'id_groupe'   => 'required|integer|min:0',
        ]);

        $campagne = Campagne::findOrFail($request->campagne_id);

        $participant = SessionParticipant::create([
            'id_session' => $campagne->id,
            'pseudo'     => trim($request->pseudo),
            'id_groupe'  => $request->id_groupe,
        ]);

        session([
            'participant' => [
                'id'           => $participant->id,
                'pseudo'       => $participant->pseudo,
                'id_groupe'    => $participant->id_groupe,
                'id_session'   => $campagne->id,
                'campagne_nom' => $campagne->nom,
                'nb_groupes'   => $campagne->nb_groupes,
            ],
        ]);

        return response()->json(['redirect' => route('participant.analyses')]);
    }

    public function analyses()
    {
        $p = session('participant');
        $coursDEaux = $this->getAnalysesForParticipant($p);

        return view('participant.analyses', [
            'participant' => $p,
            'coursDEaux'  => $coursDEaux,
        ]);
    }

    public function map()
    {
        /** @var \Illuminate\View\View $view */
        $view = app(\App\Http\Controllers\MapController::class)->index();
        return view('participant.map', $view->getData());
    }

    public function statistiques()
    {
        /** @var \Illuminate\View\View $view */
        $view = app(\App\Http\Controllers\StatistiqueController::class)->index();
        return view('participant.statistiques', $view->getData());
    }

    public function logout()
    {
        session()->forget('participant');
        return redirect('/');
    }

    // ─── helpers ──────────────────────────────────────────────────────────────

    private function getAnalysesForParticipant(array $p): \Illuminate\Support\Collection
    {
        if ($p['nb_groupes'] === 0) {
            $participantIds = [$p['id']];
        } else {
            $participantIds = SessionParticipant::where('id_session', $p['id_session'])
                ->where('id_groupe', $p['id_groupe'])
                ->pluck('id')
                ->toArray();
        }

        $pointIds = Analyse::whereIn('participant_id', $participantIds)->pluck('point_id');

        return CoursDEau::whereHas('points', fn($q) => $q->whereIn('id', $pointIds))
            ->with(['points' => function ($q) use ($pointIds, $participantIds) {
                $q->whereIn('id', $pointIds)->with(['analyses' => function ($q2) use ($participantIds) {
                    $q2->whereIn('participant_id', $participantIds)->latest();
                }]);
            }])
            ->orderBy('nom')
            ->get()
            ->map(function ($cd) {
                $allAnalyses   = $cd->points->flatMap->analyses;
                $qualiteCounts = $allAnalyses->countBy('qualite');
                return [
                    'id'             => $cd->id,
                    'nom'            => $cd->nom,
                    'total_analyses' => $allAnalyses->count(),
                    'total_points'   => $cd->points->count(),
                    'qualite_counts' => $qualiteCounts,
                    'qualite_globale' => $this->qualiteGlobale($qualiteCounts),
                    'points'         => $cd->points->map(fn($pt) => [
                        'id'        => $pt->id,
                        'latitude'  => $pt->latitude,
                        'longitude' => $pt->longitude,
                        'ville'     => $pt->ville,
                        'analyses'  => $pt->analyses->map(fn($a) => $this->formatAnalyse($a))->values(),
                    ])->values(),
                ];
            });
    }

    private function qualiteGlobale($counts): string
    {
        $valid = ['tres_bon', 'bon', 'passable', 'mediocre', 'mauvais'];
        $best  = null;
        $max   = 0;
        foreach ($valid as $q) {
            $n = $counts[$q] ?? 0;
            if ($n > $max) { $max = $n; $best = $q; }
        }
        return $best ?? 'tres_bon';
    }

    private function formatAnalyse(Analyse $a): array
    {
        $mesures = is_string($a->mesures) ? json_decode($a->mesures, true) : ($a->mesures ?? []);
        return [
            'id'         => $a->id,
            'type'       => $a->type,
            'qualite'    => $a->qualite,
            'est_valide' => (bool) $a->est_valide,
            'note'       => $mesures['note'] ?? null,
            'bandelette' => $mesures['bandelette'] ?? null,
            'photometre' => $mesures['photometre'] ?? null,
            'date'       => $a->created_at?->format('d/m/Y'),
            'time'       => $a->created_at?->format('H:i'),
            'created_at' => $a->created_at?->toISOString(),
        ];
    }
}
