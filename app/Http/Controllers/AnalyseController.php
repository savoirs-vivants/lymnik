<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Point;
use App\Models\CoursDEau;
use App\Http\Requests\StoreAnalyseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnalyseController extends Controller
{
    public function create(Request $request)
    {
        $lat         = $request->query('lat');
        $lng         = $request->query('lng');

        return view('mobile.analyse.create', compact('lat', 'lng'));
    }

    public function store(StoreAnalyseRequest $request)
    {
        DB::transaction(function () use ($request) {

            if ($request->point_id) {
                $point = Point::findOrFail($request->point_id);
            } else {
                $point = Point::create([
                    'cours_d_eau_id' => $request->cours_d_eau_id,
                    'latitude'       => $request->latitude,
                    'longitude'      => $request->longitude,
                ]);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('analyses', 'public');
            }

            $mesures = ['note' => $request->note];
            if (in_array($request->type, ['bandelette', 'les_deux'])) {
                $mesures['bandelette'] = array_map(
                    fn($v) => $v !== '' && $v !== null ? (float) $v : null,
                    $request->input('mesures.bandelette', [])
                );
            }
            if (in_array($request->type, ['photometre', 'les_deux'])) {
                $mesures['photometre'] = array_map(
                    fn($v) => $v !== '' && $v !== null ? (float) $v : null,
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

        return redirect()->route('index_mobile')->with('success', 'Mesure mise à jour !');
    }
}
