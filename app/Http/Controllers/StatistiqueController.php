<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        $coursDEaux = DB::table('cours_d_eaus')
            ->join('points', 'cours_d_eaus.id', '=', 'points.cours_d_eau_id')
            ->join('analyses', 'points.id', '=', 'analyses.point_id')
            ->select('cours_d_eaus.id', 'cours_d_eaus.nom')
            ->distinct()
            ->orderBy('cours_d_eaus.nom')
            ->get();

        $analysesRaw = DB::table('analyses')
            ->join('points', 'analyses.point_id', '=', 'points.id')
            ->join('cours_d_eaus', 'points.cours_d_eau_id', '=', 'cours_d_eaus.id')
            ->select(
                'analyses.id',
                'analyses.created_at',
                'analyses.mesures',
                'analyses.qualite',
                'analyses.type',
                'cours_d_eaus.id as cours_d_eau_id',
                'cours_d_eaus.nom as cours_d_eau_nom'
            )
            ->orderBy('analyses.created_at')
            ->get();

        $analyses = $analysesRaw->map(function ($a) {
            $m = is_string($a->mesures) ? json_decode($a->mesures, true) : [];
            $b = $m['bandelette'] ?? [];
            $p = $m['photometre'] ?? [];
            $qualite = $a->qualite ?: 'passable';

            return [
                'id'              => $a->id,
                'date'            => substr($a->created_at, 0, 10),
                'cours_d_eau_id'  => $a->cours_d_eau_id,
                'cours_d_eau_nom' => $a->cours_d_eau_nom,
                'qualite'         => $qualite, 
                'type'            => $a->type,
                'nitrates'        => isset($b['nitrates'])      ? (float) $b['nitrates']      : null,
                'nitrites'        => isset($b['nitrites'])      ? (float) $b['nitrites']      : null,
                'ph'              => isset($b['ph'])            ? (float) $b['ph']            : null,
                'durete'          => isset($b['durete_totale']) ? (float) $b['durete_totale'] : null,
                'chlore'          => isset($b['chlore'])        ? (float) $b['chlore']        : null,
                'phosphate'       => isset($p['phosphate'])     ? (float) $p['phosphate']     : null,
                'ammoniaque'      => isset($p['ammoniaque'])    ? (float) $p['ammoniaque']    : (isset($p['ammoniac']) ? (float) $p['ammoniac'] : null),
                'nitrate_photo'   => isset($p['nitrate'])       ? (float) $p['nitrate']       : null,
            ];
        });

        return view('desktop.statistiques.index', compact('coursDEaux', 'analyses'));
    }
}
