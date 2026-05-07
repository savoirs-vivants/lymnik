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
                'analyses.nom as analyse_nom',
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
                'analyse_nom'     => $a->analyse_nom ?: null,
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
                        'nom'     => $ville ?: 'Non définie',
                        'analyses' => $vItems->map(fn($a) => [
                            'id'  => $a['id'],
                            'nom' => $a['analyse_nom'] ?: 'Analyse #' . $a['id'],
                        ])->sortByDesc('id')->values()->toArray(),
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
            ->leftJoin('session_participants', 'analyses.participant_id', '=', 'session_participants.id')
            ->leftJoin('campagnes', 'analyses.session_id', '=', 'campagnes.id')
            ->leftJoin('users', 'analyses.user_id', '=', 'users.id')
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
            'cours_d_eaus.nom as cours_d_eau_nom',
            'points.ville',
            'points.latitude',
            'points.longitude',
            'campagnes.nom as campagne_nom',
            'session_participants.pseudo as participant_pseudo',
            'users.name as user_name',
            'users.firstname as user_firstname'
        )->orderBy('analyses.created_at')->get();

        $spreadsheet = new Spreadsheet();

        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Analyses Détaillées');

        $headers = [
            'ID', 'Date', 'Heure', 'Cours d\'eau', 'Ville', 'Latitude', 'Longitude',
            'Type d\'analyse', 'Qualité Globale',
            'Nitrates (mg/L)', 'Nitrites (mg/L)', 'pH', 'Chlore (mg/L)', 'Dureté (mg/L)',
            'Phosphate (mg/L)', 'Ammoniaque (mg/L)',
            'Campagne', 'Saisi par', 'Observations / Notes'
        ];
        $sheet1->fromArray($headers, NULL, 'A1');
        $sheet1->getStyle('A1:S1')->getFont()->setBold(true);
        $sheet1->getStyle('A1:S1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');

        $row = 2;
        $statsData = [];

        foreach ($analysesRaw as $a) {
            $m = is_string($a->mesures) ? json_decode($a->mesures, true) : [];
            $b = $m['bandelette'] ?? [];
            $p = $m['photometre'] ?? [];
            $qs = $a->qualite ?: 'passable';

            $dateObj = new \DateTime($a->created_at);

            $auteur = trim(($a->user_firstname ?? '') . ' ' . ($a->user_name ?? ''));
            if (empty($auteur)) $auteur = $a->participant_pseudo ?? 'Inconnu';

            $val_nitrates = $b['nitrates'] ?? null;
            $val_nitrites = $b['nitrites'] ?? null;
            $val_ph = $b['ph'] ?? null;
            $val_chlore = $b['chlore'] ?? null;
            $val_durete = $b['durete_totale'] ?? null;
            $val_phosphate = $p['phosphate'] ?? null;
            $val_ammoniaque = $p['ammoniaque'] ?? ($p['ammoniac'] ?? null);

            $sheet1->fromArray([
                $a->id,
                $dateObj->format('Y-m-d'),
                $dateObj->format('H:i'),
                $a->cours_d_eau_nom,
                $a->ville ?? '',
                $a->latitude ?? '',
                $a->longitude ?? '',
                ucfirst(str_replace('_', ' ', $a->type)),
                ucfirst(str_replace('_', ' ', $qs)),
                $val_nitrates,
                $val_nitrites,
                $val_ph,
                $val_chlore,
                $val_durete,
                $val_phosphate,
                $val_ammoniaque,
                $a->campagne_nom ?? 'Individuelle',
                $auteur,
                $m['note'] ?? ''
            ], NULL, 'A' . $row);

            $river = $a->cours_d_eau_nom;
            if (!isset($statsData[$river])) {
                $statsData[$river] = [
                    'count' => 0,
                    'qualite' => ['tres_bon' => 0, 'bon' => 0, 'passable' => 0, 'mediocre' => 0, 'mauvais' => 0],
                    'sommes' => ['nitrates' => 0, 'nitrites' => 0, 'ph' => 0, 'chlore' => 0, 'durete' => 0, 'phosphate' => 0, 'ammoniaque' => 0],
                    'comptes' => ['nitrates' => 0, 'nitrites' => 0, 'ph' => 0, 'chlore' => 0, 'durete' => 0, 'phosphate' => 0, 'ammoniaque' => 0]
                ];
            }

            $statsData[$river]['count']++;

            $qKey = strtolower(str_replace(['é', 'è', ' '], ['e', 'e', '_'], $qs));
            if (isset($statsData[$river]['qualite'][$qKey])) {
                $statsData[$river]['qualite'][$qKey]++;
            }

            $params = ['nitrates' => $val_nitrates, 'nitrites' => $val_nitrites, 'ph' => $val_ph, 'chlore' => $val_chlore, 'durete' => $val_durete, 'phosphate' => $val_phosphate, 'ammoniaque' => $val_ammoniaque];
            foreach ($params as $key => $val) {
                if ($val !== null && $val !== '') {
                    $statsData[$river]['sommes'][$key] += (float) $val;
                    $statsData[$river]['comptes'][$key]++;
                }
            }

            $row++;
        }
        foreach (range('A', 'S') as $col) $sheet1->getColumnDimension($col)->setAutoSize(true);

        if ($format === 'xlsx') {
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Moyennes & Bilan global');

            $headers2 = [
                'Cours d\'eau', 'Total Analyses',
                'Qté Très Bon', 'Qté Bon', 'Qté Passable', 'Qté Médiocre', 'Qté Mauvais',
                'Moyenne pH', 'Moy. Nitrates (mg/L)', 'Moy. Nitrites (mg/L)',
                'Moy. Chlore (mg/L)', 'Moy. Dureté (mg/L)', 'Moy. Phosphate (mg/L)', 'Moy. Ammoniaque (mg/L)'
            ];
            $sheet2->fromArray($headers2, NULL, 'A1');
            $sheet2->getStyle('A1:N1')->getFont()->setBold(true);
            $sheet2->getStyle('A1:N1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');

            $row2 = 2;
            foreach ($statsData as $riverName => $data) {
                $calcMoyenne = function($key) use ($data) {
                    return $data['comptes'][$key] > 0 ? round($data['sommes'][$key] / $data['comptes'][$key], 2) : 'N/A';
                };

                $sheet2->fromArray([
                    $riverName,
                    $data['count'],
                    $data['qualite']['tres_bon'], $data['qualite']['bon'], $data['qualite']['passable'], $data['qualite']['mediocre'], $data['qualite']['mauvais'],
                    $calcMoyenne('ph'),
                    $calcMoyenne('nitrates'),
                    $calcMoyenne('nitrites'),
                    $calcMoyenne('chlore'),
                    $calcMoyenne('durete'),
                    $calcMoyenne('phosphate'),
                    $calcMoyenne('ammoniaque')
                ], NULL, 'A' . $row2);
                $row2++;
            }
            foreach (range('A', 'N') as $col) $sheet2->getColumnDimension($col)->setAutoSize(true);
            $spreadsheet->setActiveSheetIndex(0);
        }

        $fileName = 'Export_Lymnik_' . date('Ymd_Hi') . '.' . $format;
        return response()->streamDownload(function () use ($spreadsheet, $format) {
            $writer = $format === 'csv' ? new Csv($spreadsheet) : new Xlsx($spreadsheet);
            if ($format === 'csv') {
                $writer->setDelimiter(';');
                $writer->setEnclosure('"');
                $writer->setUseBOM(true);
            }
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'        => $format === 'csv' ? 'text/csv; charset=UTF-8' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
