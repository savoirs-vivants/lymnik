<?php

namespace App\Http\Controllers;

use App\Enums\AnalyseType;
use App\Models\Analyse;
use App\Models\CoursDEau;
use App\Models\Point;
use App\Services\CoursDEauService;
use App\Services\QualiteService;
use App\Http\Requests\StoreAnalyseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnalyseController extends Controller
{
    public function __construct(private QualiteService $qualiteService) {}

    public function invalides()
    {
        $analyses = Analyse::with(['point.coursDEau', 'user'])
            ->where('est_valide', false)
            ->latest()
            ->get()
            ->map(fn($a) => $this->formatAnalyseInvalide($a));

        return response()->json($analyses);
    }

    public function valider(Analyse $analyse)
    {
        $analyse->update(['est_valide' => true]);

        return response()->json([
            'ok'            => true,
            'remaining'     => Analyse::where('est_valide', false)->count(),
        ]);
    }

    private function formatAnalyseInvalide(Analyse $a): array
    {
        $mesures = is_string($a->mesures) ? json_decode($a->mesures, true) : ($a->mesures ?? []);
        $b = $mesures['bandelette'] ?? [];
        $p = $mesures['photometre'] ?? [];

        $allMesures = [];
        $labels = [
            'nitrates'      => ['label' => 'Nitrates',       'unit' => 'mg/L'],
            'nitrites'      => ['label' => 'Nitrites',        'unit' => 'mg/L'],
            'durete_totale' => ['label' => 'Dureté totale',   'unit' => 'mg/L'],
            'durete_carb'   => ['label' => 'Dureté carb.',    'unit' => 'mg/L'],
            'ph'            => ['label' => 'pH',              'unit' => ''],
            'chlore'        => ['label' => 'Chlore',          'unit' => 'mg/L'],
        ];
        foreach ($labels as $key => $meta) {
            if (isset($b[$key])) {
                $allMesures[] = ['label' => $meta['label'], 'value' => $b[$key], 'unit' => $meta['unit']];
            }
        }
        $photoLabels = [
            'phosphate'  => ['label' => 'Phosphate',  'unit' => 'mg/L'],
            'nitrate'    => ['label' => 'Nitrate',     'unit' => 'mg/L'],
            'ammoniaque' => ['label' => 'Ammoniaque',  'unit' => 'mg/L'],
        ];
        foreach ($photoLabels as $key => $meta) {
            if (isset($p[$key])) {
                $allMesures[] = ['label' => $meta['label'], 'value' => $p[$key], 'unit' => $meta['unit']];
            }
        }

        return [
            'id'          => $a->id,
            'qualite'     => $a->qualite,
            'type'        => $a->type,
            'date'        => $a->created_at?->translatedFormat('d M Y'),
            'time'        => $a->created_at?->format('H:i'),
            'image'       => $a->image ? asset('storage/' . $a->image) : null,
            'note'        => $mesures['note'] ?? null,
            'mesures'     => $allMesures,
            'cours_d_eau' => $a->point?->coursDEau?->nom ?? 'Inconnu',
            'latitude'    => $a->point ? (float) $a->point->latitude  : null,
            'longitude'   => $a->point ? (float) $a->point->longitude : null,
            'user'        => trim(($a->user?->firstname ?? '') . ' ' . ($a->user?->name ?? '')),
        ];
    }

    public function index(Request $request)
    {
        $mode = $request->query('mode', 'participants');

        $coursDEaux = CoursDEau::whereHas('points.analyses', function ($q) use ($mode) {
            if ($mode === 'campagnes') {
                $q->whereNotNull('session_id');
            } else {
                $q->whereNull('session_id');
            }
        })
            ->with(['points' => function ($q) use ($mode) {
                $q->whereHas('analyses', function ($q2) use ($mode) {
                    if ($mode === 'campagnes') {
                        $q2->whereNotNull('session_id');
                    } else {
                        $q2->whereNull('session_id');
                    }
                })
                    ->with(['analyses' => function ($q3) use ($mode) {
                        if ($mode === 'campagnes') {
                            $q3->whereNotNull('session_id');
                        } else {
                            $q3->whereNull('session_id');
                        }
                        $q3->latest()->with('user');
                    }]);
            }])
            ->orderBy('nom')
            ->get()
            ->map(function ($cd) {
                $allAnalyses = $cd->points->flatMap->analyses;
                $qualiteCounts = $allAnalyses->countBy('qualite');

                return [
                    'id'              => $cd->id,
                    'nom'             => $cd->nom,
                    'type_cours'      => $cd->type_cours,
                    'total_analyses'  => $allAnalyses->count(),
                    'total_points'    => $cd->points->count(),
                    'qualite_counts'  => $qualiteCounts,
                    'qualite_globale' => $this->qualiteGlobale($qualiteCounts),
                    'derniere_date'   => $allAnalyses->sortByDesc('created_at')->first()?->created_at,
                    'points'          => $cd->points->map(function ($pt) {
                        $analyses = $pt->analyses
                            ->sortByDesc('created_at')
                            ->sortBy(fn($a) => (Auth::user()?->role !== 'admin' && $a->user_id === Auth::id()) ? 0 : 1)
                            ->values();
                        return [
                            'id'        => $pt->id,
                            'latitude'  => $pt->latitude,
                            'longitude' => $pt->longitude,
                            'ville'     => $pt->ville,
                            'analyses'  => $analyses->map(fn($a) => $this->formatAnalyse($a)),
                        ];
                    })->values(),
                ];
            });

        return view('desktop.analyses.index', compact('coursDEaux', 'mode'));
    }

    private function qualiteGlobale($counts): string
    {
        $valid = ['tres_bon', 'bon', 'passable', 'mediocre', 'mauvais'];
        $best  = null;
        $max   = 0;
        foreach ($valid as $q) {
            $n = $counts[$q] ?? 0;
            if ($n > $max) {
                $max = $n;
                $best = $q;
            }
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
            'image'      => $a->image ? asset('storage/' . $a->image) : null,
            'note'       => $mesures['note'] ?? null,
            'bandelette' => $mesures['bandelette'] ?? null,
            'photometre' => $mesures['photometre'] ?? null,
            'user'       => trim($a->user?->firstname . ' ' . $a->user?->name) ?: $a->nom,
            'date'       => $a->created_at?->translatedFormat('d M Y'),
            'time'       => $a->created_at?->format('H:i'),
            'created_at' => $a->created_at?->toISOString(),
            'session_id'     => $a->session_id,
            'participant_id' => $a->participant_id,
        ];
    }

    public function create(\Illuminate\Http\Request $request)
    {
        $lat         = $request->query('lat');
        $lng         = $request->query('lng');
        $coursDEauId = $request->query('cours_d_eau_id');
        $nomCoursEau = $request->query('nom_cours_eau');

        $mesures = [];

        return view('analyse', compact('lat', 'lng', 'coursDEauId', 'nomCoursEau', 'mesures'));
    }

    public function store(StoreAnalyseRequest $request, CoursDEauService $service)
    {
        DB::transaction(function () use ($request, $service) {
            if ($request->point_id) {
                $point = Point::findOrFail($request->point_id);
                $updates = [];
                if (! $point->cours_d_eau_id) {
                    $coursDEauId = $request->integer('cours_d_eau_id') ?: null;
                    if (! $coursDEauId) {
                        $river       = $service->findNearest($point->latitude, $point->longitude);
                        $coursDEauId = $river?->id;
                    }
                    if ($coursDEauId) $updates['cours_d_eau_id'] = $coursDEauId;
                }
                if (! $point->ville && $request->filled('ville')) {
                    $updates['ville'] = $request->ville;
                }
                if ($updates) $point->update($updates);
            } else {
                $coursDEauId = $request->integer('cours_d_eau_id') ?: null;
                if (! $coursDEauId) {
                    $river       = $service->findNearest($request->latitude, $request->longitude);
                    $coursDEauId = $river?->id;
                }
                $point = Point::create([
                    'latitude'       => $request->latitude,
                    'longitude'      => $request->longitude,
                    'cours_d_eau_id' => $coursDEauId,
                    'ville'          => $request->ville,
                ]);
            }

            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('analyses', 'public')
                : null;

            $mesures = ['note' => $request->note];

            $type = AnalyseType::from($request->type);

            if ($type->hasBandelette()) {
                $mesures['bandelette'] = array_map(
                    fn($v) => ($v !== '' && $v !== null) ? (float) $v : null,
                    $request->input('mesures.bandelette', [])
                );
            }
            if ($type->hasPhotometre()) {
                $mesures['photometre'] = array_map(
                    fn($v) => ($v !== '' && $v !== null) ? (float) $v : null,
                    $request->input('mesures.photometre', [])
                );
            }

            $qualite     = $this->qualiteService->calculer($mesures);
            $participant = session('participant');

            $analyse = Analyse::create([
                'point_id'       => $point->id,
                'type'           => $request->type,
                'image'          => $imagePath,
                'mesures'        => json_encode($mesures),
                'est_valide'     => $this->qualiteService->isValid($mesures),
                'qualite'        => $qualite,
                'user_id'        => Auth::id(),
                'participant_id' => $participant['id']         ?? null,
                'session_id'     => $participant['id_session'] ?? Auth::user()?->active_campagne_id,
                'nom'            => $request->filled('nom') ? trim($request->nom) : null,
            ]);

            if ($request->filled('date_prelevement')) {
                DB::table('analyses')
                    ->where('id', $analyse->id)
                    ->update(['created_at' => \Carbon\Carbon::parse($request->date_prelevement)]);
            }
        });

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        $redirectTo = $request->input('redirect_to');

        if ($redirectTo && is_string($redirectTo) && str_starts_with($redirectTo, '/') && !str_contains($redirectTo, '://')) {
            $separator = str_contains($redirectTo, '?') ? '&' : '?';
            $redirectWithCoords = $redirectTo . $separator . "lat={$lat}&lng={$lng}";

            return redirect($redirectWithCoords)->with('success', 'Analyse enregistrée !');
        }

        return redirect()->route('mobile', [
            'lat' => $lat,
            'lng' => $lng
        ])->with('success', 'Analyse enregistrée !');
    }

    private function authorizeOwner(Analyse $analyse): void
    {
        if (Auth::user()->role !== 'admin' && $analyse->user_id !== Auth::id()) {
            abort(403);
        }
    }

    public function edit(Analyse $analyse)
    {
        $this->authorizeOwner($analyse);

        $analyse->load('point.coursDEau');
        $point = $analyse->point;
        $mesures = is_string($analyse->mesures) ? json_decode($analyse->mesures, true) : ($analyse->mesures ?? []);

        return view('analyse', [
            'analyse'     => $analyse,
            'mesures'     => $mesures,
            'lat'         => $point->latitude,
            'lng'         => $point->longitude,
            'coursDEauId' => $point->cours_d_eau_id,
            'nomCoursEau' => $point->coursDEau?->nom,
        ]);
    }

    public function update(StoreAnalyseRequest $request, Analyse $analyse, CoursDEauService $service)
    {
        $this->authorizeOwner($analyse);

        DB::transaction(function () use ($request, $analyse, $service) {
            $point = $analyse->point;

            $coursDEauId = $request->integer('cours_d_eau_id') ?: null;
            if (! $coursDEauId) {
                $river       = $service->findNearest($request->latitude, $request->longitude);
                $coursDEauId = $river?->id;
            }
            $point->update([
                'latitude'       => $request->latitude,
                'longitude'      => $request->longitude,
                'cours_d_eau_id' => $coursDEauId,
                'ville'          => $request->filled('ville') ? $request->ville : $point->ville,
            ]);

            if ($request->hasFile('image')) {
                if ($analyse->image) {
                    Storage::disk('public')->delete($analyse->image);
                }
                $imagePath = $request->file('image')->store('analyses', 'public');
            } else {
                $imagePath = $analyse->image;
            }

            $mesures = ['note' => $request->note];

            $type = AnalyseType::from($request->type);

            if ($type->hasBandelette()) {
                $mesures['bandelette'] = array_map(
                    fn($v) => ($v !== '' && $v !== null) ? (float) $v : null,
                    $request->input('mesures.bandelette', [])
                );
            }
            if ($type->hasPhotometre()) {
                $mesures['photometre'] = array_map(
                    fn($v) => ($v !== '' && $v !== null) ? (float) $v : null,
                    $request->input('mesures.photometre', [])
                );
            }

            $qualite = $this->qualiteService->calculer($mesures);

            $analyse->update([
                'type'       => $request->type,
                'image'      => $imagePath,
                'mesures'    => json_encode($mesures),
                'est_valide' => $this->qualiteService->isValid($mesures),
                'qualite'    => $qualite,
                'nom'        => $request->filled('nom') ? trim($request->nom) : null,
            ]);

            if ($request->filled('date_prelevement')) {
                DB::table('analyses')
                    ->where('id', $analyse->id)
                    ->update(['created_at' => \Carbon\Carbon::parse($request->date_prelevement)]);
            }
        });

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        $redirectTo = $request->input('redirect_to');

        if ($redirectTo && is_string($redirectTo) && str_starts_with($redirectTo, '/') && !str_contains($redirectTo, '://')) {
            $separator = str_contains($redirectTo, '?') ? '&' : '?';
            $redirectWithCoords = $redirectTo . $separator . "lat={$lat}&lng={$lng}";

            return redirect($redirectWithCoords)->with('success', 'Analyse modifiée !');
        }

        return redirect()->route('mobile', [
            'lat' => $lat,
            'lng' => $lng,
        ])->with('success', 'Analyse modifiée !');
    }

    public function destroy(Analyse $analyse)
    {
        $this->authorizeOwner($analyse);

        $point = $analyse->point;

        foreach ($point->analyses as $a) {
            if ($a->image) {
                Storage::disk('public')->delete($a->image);
            }
        }

        $point->delete();

        return response()->json(['ok' => true]);
    }

    public function myAnalyses()
    {
        $analyses = Analyse::with(['point.coursDEau'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($a) {
                $mesures = is_string($a->mesures) ? json_decode($a->mesures, true) : ($a->mesures ?? []);

                return [
                    'id'          => $a->id,
                    'type'        => $a->type,
                    'est_valide'  => (bool) $a->est_valide,
                    'qualite'     => $a->qualite,
                    'image'       => $a->image ? asset('storage/' . $a->image) : null,
                    'note'        => $mesures['note'] ?? null,
                    'mesures'     => $mesures,
                    'created_at'  => $a->created_at?->translatedFormat('d M Y'),
                    'time'        => $a->created_at?->format('H\hi'),
                    'cours_d_eau' => $a->point?->coursDEau?->nom ?? 'Cours d\'eau inconnu',
                    'localite'    => $a->point?->coursDEau?->localite ?? null,
                    'latitude'    => $a->point ? (float) $a->point->latitude  : null,
                    'longitude'   => $a->point ? (float) $a->point->longitude : null,
                    'session'     => $a->session_id ? 'Session ' . $a->session_id : null,
                ];
            });

        $count = $analyses->count();
        $month = now()->translatedFormat('M Y');

        return view('mobile.analyse.index', compact('analyses', 'count', 'month'));
    }
}
