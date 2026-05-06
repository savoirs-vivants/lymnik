<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class StatistiqueController extends Controller
{
    public function index()
    {
        $analysesRaw = DB::table('analyses')
            ->join('points', 'analyses.point_id', '=', 'points.id')
            ->join('cours_d_eaus', 'points.cours_d_eau_id', '=', 'cours_d_eaus.id')
            ->leftJoin('session_participants', 'analyses.participant_id', '=', 'session_participants.id')
            ->leftJoin('campagnes', 'analyses.session_id', '=', 'campagnes.id')
            ->select(
                'analyses.id',
                'analyses.created_at',
                'analyses.mesures',
                'analyses.qualite',
                'analyses.type',
                'analyses.point_id',
                'cours_d_eaus.id as cours_d_eau_id',
                'cours_d_eaus.nom as cours_d_eau_nom',
                'points.ville',
                DB::raw('COALESCE(session_participants.id_groupe, 0) as id_groupe'),
                'session_participants.pseudo',
                'campagnes.id as campagne_id',
                'campagnes.nom as campagne_nom',
            )
            ->orderBy('analyses.created_at')
            ->get();

        $analyses = $analysesRaw->map(function ($a) {
            $m = is_string($a->mesures) ? json_decode($a->mesures, true) : [];
            $b = $m['bandelette'] ?? [];
            $p = $m['photometre'] ?? [];

            return [
                'id'              => $a->id,
                'date'            => substr($a->created_at, 0, 10),
                'cours_d_eau_id'  => $a->cours_d_eau_id,
                'cours_d_eau_nom' => $a->cours_d_eau_nom,
                'ville'           => $a->ville ?: null,
                'point_id'        => $a->point_id,
                'campagne_id'     => $a->campagne_id,
                'campagne_nom'    => $a->campagne_nom,
                'id_groupe'       => (int) $a->id_groupe,
                'pseudo'          => $a->pseudo,
                'qualite'         => $a->qualite ?: 'passable',
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

        $riversTree = $analyses->groupBy('cours_d_eau_id')->map(function ($items) {
            return [
                'id'    => $items->first()['cours_d_eau_id'],
                'nom'   => $items->first()['cours_d_eau_nom'],
                'villes' => $items->groupBy('ville')->map(function ($vItems, $ville) {
                    return [
                        'nom'    => $ville ?: 'Non définie',
                        'points' => $vItems->pluck('point_id')->unique()->sort()->values()->toArray(),
                    ];
                })->values()->toArray(),
            ];
        })->values();

        $campagnesTree = $analyses->whereNotNull('campagne_id')->groupBy('campagne_id')->map(function ($items) {
            $groupes = $items->pluck('id_groupe')->unique()->sort()->values();
            return [
                'id'      => $items->first()['campagne_id'],
                'nom'     => $items->first()['campagne_nom'],
                'groupes' => $groupes->toArray(),
            ];
        })->values();

        $coursDEaux = $analyses->groupBy('cours_d_eau_id')->map(fn($items) => (object)[
            'id'  => $items->first()['cours_d_eau_id'],
            'nom' => $items->first()['cours_d_eau_nom'],
        ])->values();

        return view('desktop.statistiques.index', compact('coursDEaux', 'analyses', 'riversTree', 'campagnesTree'));
    }

    public function export(Request $request)
    {
        $rivers    = $request->input('rivers', []);
        $dateStart = $request->input('date_start');
        $dateEnd   = $request->input('date_end');
        $format    = $request->input('format', 'xlsx');

        $query = DB::table('analyses')
            ->join('points', 'analyses.point_id', '=', 'points.id')
            ->join('cours_d_eaus', 'points.cours_d_eau_id', '=', 'cours_d_eaus.id')
            ->where('analyses.est_valide', true);

        if (!empty($rivers)) $query->whereIn('cours_d_eaus.id', $rivers);
        if ($dateStart)      $query->whereDate('analyses.created_at', '>=', $dateStart);
        if ($dateEnd)        $query->whereDate('analyses.created_at', '<=', $dateEnd);

        $analysesRaw = $query->select(
            'analyses.id',
            'analyses.created_at',
            'analyses.mesures',
            'analyses.qualite',
            'analyses.type',
            'cours_d_eaus.nom as cours_d_eau_nom'
        )->orderBy('analyses.created_at')->get();

        $spreadsheet = new Spreadsheet();
        $sheet1      = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Analyses & Mesures');
        $headers = ['ID', 'Date', 'Cours d\'eau', 'Type', 'Qualité', 'Nitrates (mg/L)', 'Nitrites (mg/L)', 'pH', 'Chlore (mg/L)', 'Dureté (mg/L)', 'Phosphate (mg/L)', 'Ammoniaque (mg/L)'];
        $sheet1->fromArray($headers, NULL, 'A1');
        $sheet1->getStyle('A1:L1')->getFont()->setBold(true);

        $row = 2;
        $qualiteStats = [];
        foreach ($analysesRaw as $a) {
            $m = is_string($a->mesures) ? json_decode($a->mesures, true) : [];
            $b = $m['bandelette'] ?? [];
            $p = $m['photometre'] ?? [];
            $qs = $a->qualite ?: 'passable';
            $sheet1->fromArray([
                $a->id,
                substr($a->created_at, 0, 10),
                $a->cours_d_eau_nom,
                $a->type,
                $qs,
                $b['nitrates'] ?? '',
                $b['nitrites'] ?? '',
                $b['ph'] ?? '',
                $b['chlore'] ?? '',
                $b['durete_totale'] ?? '',
                $p['phosphate'] ?? '',
                $p['ammoniaque'] ?? ($p['ammoniac'] ?? ''),
            ], NULL, 'A' . $row);
            if (!isset($qualiteStats[$a->cours_d_eau_nom])) {
                $qualiteStats[$a->cours_d_eau_nom] = ['tres_bon' => 0, 'bon' => 0, 'passable' => 0, 'mediocre' => 0, 'mauvais' => 0];
            }
            $qKey = strtolower(str_replace(['é', 'è', ' '], ['e', 'e', '_'], $qs));
            if (isset($qualiteStats[$a->cours_d_eau_nom][$qKey])) $qualiteStats[$a->cours_d_eau_nom][$qKey]++;
            $row++;
        }
        foreach (range('A', 'L') as $col) $sheet1->getColumnDimension($col)->setAutoSize(true);

        if ($format === 'xlsx') {
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Distribution Qualité');
            $sheet2->fromArray(['Cours d\'eau', 'Très Bon', 'Bon', 'Passable', 'Médiocre', 'Mauvais'], NULL, 'A1');
            $sheet2->getStyle('A1:F1')->getFont()->setBold(true);
            $row2 = 2;
            foreach ($qualiteStats as $riverName => $stats) {
                $sheet2->fromArray([$riverName, $stats['tres_bon'], $stats['bon'], $stats['passable'], $stats['mediocre'], $stats['mauvais']], NULL, 'A' . $row2);
                $row2++;
            }
            foreach (range('A', 'F') as $col) $sheet2->getColumnDimension($col)->setAutoSize(true);
            $spreadsheet->setActiveSheetIndex(0);
        }

        $fileName = 'Export_Statistiques_' . date('Y-m-d_H-i') . '.' . $format;
        return response()->streamDownload(function () use ($spreadsheet, $format) {
            $writer = $format === 'csv' ? new Csv($spreadsheet) : new Xlsx($spreadsheet);
            if ($format === 'csv') {
                $writer->setDelimiter(';');
                $writer->setEnclosure('"');
            }
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
