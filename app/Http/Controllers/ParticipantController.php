<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Campagne;
use App\Models\CoursDEau;
use App\Models\Point;
use App\Models\SessionParticipant;
use App\Services\CoursDEauService;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function showJoin()
    {
        if (session()->has('participant')) {
            return redirect()->route('participant.analyses');
        }
        return view('participant.join');
    }

    public function validateCode(Request $request)
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

    public function register(Request $request)
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
        $p = session('participant');
        $points = $this->getPointsForParticipant($p);

        return view('participant.map', [
            'participant' => $p,
            'points'      => $points,
        ]);
    }

    public function comparer()
    {
        $p = session('participant');
        $campagne = Campagne::with('participants')->findOrFail($p['id_session']);

        $groupesData = $this->buildGroupesData($campagne, $p['nb_groupes']);

        return view('participant.comparer', [
            'participant' => $p,
            'campagne'    => $campagne,
            'groupesData' => $groupesData,
        ]);
    }

    public function storeAnalyse(Request $request, CoursDEauService $service)
    {
        $request->validate([
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'type'         => 'required|in:bandelette,photometre,les_deux',
        ]);

        $p = session('participant');

        $coursDEauId = $request->integer('cours_d_eau_id') ?: null;
        if (! $coursDEauId) {
            $river       = $service->findNearest($request->latitude, $request->longitude);
            $coursDEauId = $river?->id;
        }

        $point = null;
        if ($request->filled('point_id')) {
            $point = Point::find($request->point_id);
        }

        if (! $point) {
            $point = Point::create([
                'latitude'       => $request->latitude,
                'longitude'      => $request->longitude,
                'cours_d_eau_id' => $coursDEauId,
                'ville'          => $request->ville,
            ]);
        }

        $mesures = ['note' => $request->note];

        if (in_array($request->type, ['bandelette', 'les_deux'])) {
            $mesures['bandelette'] = array_map(
                fn($v) => ($v !== '' && $v !== null) ? (float) $v : null,
                $request->input('mesures.bandelette', [])
            );
        }
        if (in_array($request->type, ['photometre', 'les_deux'])) {
            $mesures['photometre'] = array_map(
                fn($v) => ($v !== '' && $v !== null) ? (float) $v : null,
                $request->input('mesures.photometre', [])
            );
        }

        $qualite = $this->calculerQualite($mesures);

        $analyse = Analyse::create([
            'point_id'       => $point->id,
            'type'           => $request->type,
            'mesures'        => json_encode($mesures),
            'est_valide'     => true,
            'qualite'        => $qualite,
            'user_id'        => null,
            'participant_id' => $p['id'],
            'session_id'     => $p['id_session'],
        ]);

        return response()->json([
            'ok'      => true,
            'analyse' => [
                'id'       => $analyse->id,
                'qualite'  => $qualite,
                'type'     => $analyse->type,
                'point_id' => $point->id,
                'lat'      => (float) $point->latitude,
                'lng'      => (float) $point->longitude,
            ],
        ]);
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
            // Mode individuel : seulement ce participant
            $analyseQuery = Analyse::where('participant_id', $p['id']);
        } else {
            // Mode groupe : tous les membres du même groupe
            $participantIds = SessionParticipant::where('id_session', $p['id_session'])
                ->where('id_groupe', $p['id_groupe'])
                ->pluck('id');
            $analyseQuery = Analyse::whereIn('participant_id', $participantIds);
        }

        $analyseIds = $analyseQuery->pluck('point_id');

        return CoursDEau::whereHas('points', fn($q) => $q->whereIn('id', $analyseIds))
            ->with(['points' => function ($q) use ($analyseQuery) {
                $ids = $analyseQuery->pluck('point_id');
                $q->whereIn('id', $ids)->with(['analyses' => function ($q2) use ($analyseQuery) {
                    $participantIds = $analyseQuery->pluck('participant_id');
                    $q2->whereIn('participant_id', $participantIds)->latest();
                }]);
            }])
            ->orderBy('nom')
            ->get()
            ->map(function ($cd) {
                $allAnalyses = $cd->points->flatMap->analyses;
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

    private function getPointsForParticipant(array $p): array
    {
        if ($p['nb_groupes'] === 0) {
            $participantIds = [$p['id']];
        } else {
            $participantIds = SessionParticipant::where('id_session', $p['id_session'])
                ->where('id_groupe', $p['id_groupe'])
                ->pluck('id')
                ->toArray();
        }

        $analyses = Analyse::whereIn('participant_id', $participantIds)
            ->with('point.coursDEau')
            ->get();

        return $analyses->groupBy('point_id')->map(function ($group) {
            $first = $group->first();
            return [
                'id'         => $first->point_id,
                'latitude'   => (float) $first->point->latitude,
                'longitude'  => (float) $first->point->longitude,
                'cours_eau'  => $first->point->coursDEau?->nom ?? '',
                'analyses'   => $group->map(fn($a) => $this->formatAnalyse($a))->values(),
            ];
        })->values()->toArray();
    }

    private function buildGroupesData(Campagne $campagne, int $nbGroupes): array
    {
        $qualiteOrder = ['tres_bon' => 0, 'bon' => 1, 'passable' => 2, 'mediocre' => 3, 'mauvais' => 4];
        $result = [];

        if ($nbGroupes === 0) {
            // Comparer par participant individuel
            foreach ($campagne->participants as $participant) {
                $analyses = Analyse::where('participant_id', $participant->id)
                    ->with('point.coursDEau')
                    ->latest()
                    ->get();

                $result[] = [
                    'label'    => $participant->pseudo,
                    'analyses' => $this->aggregateForChart($analyses),
                ];
            }
        } else {
            for ($i = 1; $i <= $nbGroupes; $i++) {
                $label = 'Groupe ' . chr(64 + $i);
                $participantIds = $campagne->participants
                    ->where('id_groupe', $i)
                    ->pluck('id');

                $analyses = Analyse::whereIn('participant_id', $participantIds)
                    ->with('point.coursDEau')
                    ->latest()
                    ->get();

                $result[] = [
                    'label'    => $label,
                    'analyses' => $this->aggregateForChart($analyses),
                ];
            }
        }

        return $result;
    }

    private function aggregateForChart(\Illuminate\Support\Collection $analyses): array
    {
        $qualiteCounts = $analyses->countBy('qualite');
        $total = $analyses->count();

        $params = ['nitrites', 'nitrates', 'nitrate', 'phosphate', 'chlore', 'ammoniaque', 'ph'];
        $paramMeans = [];
        foreach ($params as $param) {
            $vals = $analyses->flatMap(function ($a) use ($param) {
                $mesures = is_string($a->mesures) ? json_decode($a->mesures, true) : ($a->mesures ?? []);
                $all = array_merge($mesures['bandelette'] ?? [], $mesures['photometre'] ?? []);
                return isset($all[$param]) && $all[$param] !== null ? [(float) $all[$param]] : [];
            });
            if ($vals->count()) {
                $paramMeans[$param] = round($vals->avg(), 2);
            }
        }

        return [
            'total'         => $total,
            'qualite_counts' => $qualiteCounts->toArray(),
            'param_means'   => $paramMeans,
            'timeline'      => $analyses->map(fn($a) => [
                'date'    => $a->created_at?->format('Y-m-d'),
                'qualite' => $a->qualite,
            ])->values()->toArray(),
        ];
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

    private function calculerQualite(array $mesures): string
    {
        $ordre  = ['tres_bon' => 0, 'bon' => 1, 'passable' => 2, 'mediocre' => 3, 'mauvais' => 4];
        $qualite = 'tres_bon';
        $seuils  = [
            'nitrites'   => [0.03, 0.3,  0.5,  1.0],
            'nitrates'   => [2,    10,   25,   50],
            'nitrate'    => [2,    10,   25,   50],
            'phosphate'  => [0.05, 0.2,  0.5,  1.0],
            'chlore'     => [25,   50,   100,  250],
            'ammoniaque' => [0.1,  0.5,  2.0,  5.0],
        ];

        $toutesMesures = array_merge($mesures['bandelette'] ?? [], $mesures['photometre'] ?? []);

        foreach ($toutesMesures as $key => $val) {
            if ($val === null) continue;
            $v = (float) $val;
            $q = null;

            if ($key === 'ph') {
                if ($v >= 6.5 && $v <= 8.5)      $q = 'tres_bon';
                elseif ($v >= 6.0 && $v <= 9.0)  $q = 'bon';
                elseif ($v >= 5.5 && $v <= 9.5)  $q = 'passable';
                elseif ($v >= 5.0 && $v <= 10.0) $q = 'mediocre';
                else                               $q = 'mauvais';
            } elseif (isset($seuils[$key])) {
                [$s1, $s2, $s3, $s4] = $seuils[$key];
                if      ($v <= $s1) $q = 'tres_bon';
                elseif  ($v <= $s2) $q = 'bon';
                elseif  ($v <= $s3) $q = 'passable';
                elseif  ($v <= $s4) $q = 'mediocre';
                else                $q = 'mauvais';
            }

            if ($q !== null && $ordre[$q] > $ordre[$qualite]) {
                $qualite = $q;
            }
        }

        return $qualite;
    }
}
