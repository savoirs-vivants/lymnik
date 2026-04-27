<?php

namespace App\Http\Controllers;

use App\Models\Capteur;
use App\Models\CoursDEau;
use App\Models\Point;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $capteurs = Capteur::all();
        $capteursJson = $capteurs->toJson();

        $points = Point::with([
            'analyses' => fn($q) => $q->with('user')->latest()->limit(1),
            'coursDEau:id,nom',
        ])->get();

        $pointsJson = $points->map(fn($p) => [
            'id'             => $p->id,
            'latitude'       => (float) $p->latitude,
            'longitude'      => (float) $p->longitude,
            'cours_d_eau_id' => $p->cours_d_eau_id,
            'cours_d_eau'    => $p->coursDEau?->nom,
            'ville'          => $p->ville,
            'analyse'        => $p->analyses->first() ? [
                'id'         => $p->analyses->first()->id,
                'type'       => $p->analyses->first()->type,
                'est_valide' => (bool) $p->analyses->first()->est_valide,
                'qualite'    => $p->analyses->first()->qualite,
                'user_name'  => trim(
                    ($p->analyses->first()->user?->firstname ?? '') . ' ' .
                        ($p->analyses->first()->user?->name ?? 'Participant')
                ),
                'initials'   => strtoupper(
                    substr($p->analyses->first()->user?->firstname ?? 'P', 0, 1) .
                        substr($p->analyses->first()->user?->name ?? '', 0, 1)
                ),
                'created_at' => $p->analyses->first()->created_at?->translatedFormat('d M Y'),
                'mesures'    => $p->analyses->first()->mesures,
            ] : null,
        ]);

        $riverIds   = $points->whereNotNull('cours_d_eau_id')->pluck('cours_d_eau_id')->unique()->values();
        $riversJson = CoursDEau::whereIn('id', $riverIds)
            ->select(['id', 'nom', 'trace'])
            ->get()
            ->map(fn($r) => [
                'id'       => $r->id,
                'nom'      => $r->nom,
                'geometry' => ($decoded = json_decode($r->trace, true)) && is_string($decoded)
                    ? json_decode($decoded, true)
                    : $decoded,
            ]);

        return view('desktop.map', compact('pointsJson', 'riversJson', 'capteursJson'));
    }
}
