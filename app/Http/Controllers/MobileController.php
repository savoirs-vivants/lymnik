<?php

namespace App\Http\Controllers;

use App\Models\Point;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function index()
    {
        $points = Point::with([
            'analyses' => fn($q) => $q->with('user')->latest()->limit(1),
        ])->get();

        $pointsJson = $points->map(fn($p) => [
            'id'             => $p->id,
            'latitude'       => (float) $p->latitude,
            'longitude'      => (float) $p->longitude,
            'analyse'        => $p->analyses->first() ? [
                'id'         => $p->analyses->first()->id,
                'type'       => $p->analyses->first()->type,
                'est_valide' => (bool) $p->analyses->first()->est_valide,
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

        return view('mobile.index', ['pointsJson' => $pointsJson]);
    }
}
