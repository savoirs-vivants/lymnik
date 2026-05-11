<?php

namespace App\Http\Controllers;

use App\Enums\AnalyseType;
use App\Models\Analyse;
use App\Models\CoursDEau;
use App\Models\Point;
use App\Services\CoursDEauService;
use App\Http\Requests\StoreAnalyseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyseController extends Controller
{
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
                $q->whereNotNull('session_id')->whereNotNull('participant_id');
            } else {
                $q->whereNull('session_id')->whereNull('participant_id');
            }
        })
            ->with(['points' => function ($q) use ($mode) {
                $q->whereHas('analyses', function ($q2) use ($mode) {
                    if ($mode === 'campagnes') {
                        $q2->whereNotNull('session_id')->whereNotNull('participant_id');
                    } else {
                        $q2->whereNull('session_id')->whereNull('participant_id');
                    }
                })
                    ->with(['analyses' => function ($q3) use ($mode) {
                        if ($mode === 'campagnes') {
                            $q3->whereNotNull('session_id')->whereNotNull('participant_id');
                        } else {
                            $q3->whereNull('session_id')->whereNull('participant_id');
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
                            ->sortBy(fn($a) => (auth()->user()?->role !== 'admin' && $a->user_id === auth()->id()) ? 0 : 1)
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
            'user'       => $a->user?->firstname . ' ' . $a->user?->name,
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

        return view('analyse', compact('lat', 'lng', 'coursDEauId', 'nomCoursEau'));
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

            $qualite     = $this->calculerQualite($mesures);
            $participant = session('participant');

            Analyse::create([
                'point_id'       => $point->id,
                'type'           => $request->type,
                'image'          => $imagePath,
                'mesures'        => json_encode($mesures),
                'est_valide'     => $this->isValid($mesures),
                'qualite'        => $qualite,
                'user_id'        => Auth::id(),
                'participant_id' => $participant['id']         ?? null,
                'session_id'     => $participant['id_session'] ?? null,
                'nom'            => $request->filled('nom') ? trim($request->nom) : null,
            ]);
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

    private function calculerQualite(array $mesures): string
    {
        $ordre = ['tres_bon' => 0, 'bon' => 1, 'passable' => 2, 'mediocre' => 3, 'mauvais' => 4];
        $qualite = 'tres_bon';

        $seuils = [
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
                if ($v <= $s1) $q = 'tres_bon';
                elseif ($v <= $s2) $q = 'bon';
                elseif ($v <= $s3) $q = 'passable';
                elseif ($v <= $s4) $q = 'mediocre';
                else                $q = 'mauvais';
            }

            if ($q !== null && $ordre[$q] > $ordre[$qualite]) {
                $qualite = $q;
            }
        }

        return $qualite;
    }

    private const SEUILS_VALIDITE = [
        'bandelette' => [
            'nitrates'      => 50,
            'nitrites'      => 1,
            'durete_totale' => 375,
            'durete_carb'   => 357,
            'ph'            => 20,
            'chlore'        => 500,
        ],
        'photometre' => [
            'ammoniaque'  => 5,
            'nitrate'   => 50,
            'phosphate' => 1,
        ],
    ];

    private function isValid(array $mesures): bool
    {
        foreach (self::SEUILS_VALIDITE as $type => $seuils) {
            foreach ($seuils as $key => $max) {
                $val = $mesures[$type][$key] ?? null;
                if ($val !== null && (float) $val > $max) {
                    return false;
                }
            }
        }
        return true;
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
