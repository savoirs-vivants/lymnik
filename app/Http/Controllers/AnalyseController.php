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

                if (! $point->cours_d_eau_id) {
                    $coursDEauId = $request->integer('cours_d_eau_id') ?: null;
                    if (! $coursDEauId) {
                        $river       = $service->findNearest($point->latitude, $point->longitude);
                        $coursDEauId = $river?->id;
                    }
                    if ($coursDEauId) {
                        $point->update(['cours_d_eau_id' => $coursDEauId]);
                    }
                }
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

            Analyse::create([
                'point_id'   => $point->id,
                'type'       => $request->type,
                'image'      => $imagePath,
                'mesures'    => json_encode($mesures),
                'est_valide' => false,
                'user_id'    => Auth::id(),
            ]);
        });

        return redirect()->route('index_mobile')->with('success', 'Analyse enregistrée !');
    }
}
