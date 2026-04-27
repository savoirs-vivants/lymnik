<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Point;
use App\Services\CoursDEauService;
use App\Http\Requests\StoreAnalyseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyseController extends Controller
{
    public function create(\Illuminate\Http\Request $request)
    {
        $lat         = $request->query('lat');
        $lng         = $request->query('lng');
        $coursDEauId = $request->query('cours_d_eau_id');
        $nomCoursEau = $request->query('nom_cours_eau');

        return view('mobile.analyse.create', compact('lat', 'lng', 'coursDEauId', 'nomCoursEau'));
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

            Analyse::create([
                'point_id'   => $point->id,
                'type'       => $request->type,
                'image'      => $imagePath,
                'mesures'    => json_encode($mesures),
                'est_valide' => $this->isValid($mesures),
                'qualite'    => $qualite,
                'user_id'    => Auth::id(),
            ]);
        });

        return redirect()->route('index_mobile')->with('success', 'Analyse enregistrée !');
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

    private const SEUILS_VALIDITE = [
        'bandelette' => [
            'nitrates'      => 500,
            'nitrites'      => 10,
            'durete_totale' => 375,
            'durete_carb'   => 357,
            'ph'            => 14,
            'chlore'        => 5.0,
        ],
        'photometre' => [
            'ammoniaque'  => 5,
            'nitrate'   => 500,
            'phosphate' => 5,
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
