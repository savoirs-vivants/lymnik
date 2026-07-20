<?php

namespace App\Http\Controllers;

use App\Models\Capteur;
use App\Models\CouleeDeBoue;
use App\Models\CoursDEau;
use App\Models\Point;


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
                'nom'        => $p->analyses->first()->nom,
                'user_id'    => $p->analyses->first()->user_id,
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
            ->select(['id', 'nom'])
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'nom' => $r->nom]);

        $couleesJson = CouleeDeBoue::with('user:id,firstname,name')
            ->get()
            ->map(fn($c) => [
                'id'      => $c->id,
                'lat'     => (float) $c->lat,
                'lng'     => (float) $c->lng,
                'user'    => trim($c->user?->firstname . ' ' . $c->user?->name),
                'user_id' => $c->user_id,
                'type'        => $c->type,
                'description' => $c->description,
                'image'       => $c->image ? asset('storage/' . $c->image) : null,
                'images'      => collect($c->images ?? [])->map(fn($img) => asset('storage/' . $img))->values(),
                'date'        => $c->date
                                 ? \Carbon\Carbon::parse($c->date)->translatedFormat('d M Y')
                                 : $c->created_at?->translatedFormat('d M Y'),
            ]);

        return view('desktop.map', compact('pointsJson', 'riversJson', 'capteursJson', 'couleesJson'));
    }
}
