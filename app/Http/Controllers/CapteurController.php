<?php

namespace App\Http\Controllers;

use App\Models\Capteur;
use App\Models\Mesure;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CapteurController extends Controller
{
    public function index()
    {
        $capteurs = Capteur::with(['coursDEau', 'latestMesure'])->get();

        return view('desktop.capteurs.index', compact('capteurs'));
    }

    public function show(int $id)
    {
        $capteur = Capteur::with('coursDEau')->findOrFail($id);

        $mesures = Mesure::where('capteur_id', $id)
            ->latest()
            ->take(50)
            ->get();

        $tableMesures = $mesures->take(10);

        $chartData = $mesures->sortBy('created_at')->values();

        $graphLabels       = $chartData->pluck('created_at')->map(fn($d) => $d->format('d/m H:i'));
        $graphTemp         = $chartData->pluck('temp_eau');
        $graphDebit        = $chartData->pluck('debit');
        $graphHauteur      = $chartData->pluck('hauteur');
        $graphTurbidite    = $chartData->pluck('turbidite');
        $graphConductivite = $chartData->pluck('conductivite');

        return view('desktop.capteurs.show', compact(
            'capteur',
            'mesures',
            'tableMesures',
            'graphLabels',
            'graphTemp',
            'graphDebit',
            'graphHauteur',
            'graphTurbidite',
            'graphConductivite'
        ));
    }

    public function chartData(int $id, \Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        Capteur::findOrFail($id);

        $limit = min((int) ($request->query('limit', 50)), 2000);

        $query = Mesure::where('capteur_id', $id);

        if ($request->filled('from')) {
            $query->where('created_at', '>=', Carbon::parse($request->from)->startOfDay());
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', Carbon::parse($request->to)->endOfDay());
        }

        // latest() pour prendre les N plus récentes dans la période, puis re-trier pour l'affichage
        $query = $query->latest()->limit($limit)->get()->sortBy('created_at')->values();

        return response()->json([
            'labels'       => $query->map(fn($m) => Carbon::parse($m->date_mesure_bluetooth ?? $m->created_at)->format('d/m H:i')),
            'temp'         => $query->pluck('temp_eau'),
            'debit'        => $query->pluck('debit'),
            'hauteur'      => $query->pluck('hauteur'),
            'turbidite'    => $query->pluck('turbidite'),
            'conductivite' => $query->pluck('conductivite'),
            'count'        => $query->count(),
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'latitude'       => 'required|numeric',
            'longitude'      => 'required|numeric',
            'cours_d_eau_id' => 'nullable|exists:cours_d_eaus,id',
            'type_capteur'   => 'required|in:lora,bluetooth,les_deux',
            'devEUI'         => 'nullable|string|max:255',
            'UID'            => 'nullable|string|max:255',
        ]);

        $query = Capteur::query();

        if ($request->type_capteur === 'lora') {
            if (!$request->devEUI) {
                return back()->withErrors(['devEUI' => 'Le devEUI est requis pour le mode LoRa.'])->withInput();
            }
            $query->where('devEUI', $request->devEUI);
        } elseif ($request->type_capteur === 'bluetooth') {
            if (!$request->UID) {
                return back()->withErrors(['UID' => 'L\'UID est requis pour le mode Bluetooth.'])->withInput();
            }
            $query->where('UID', $request->UID);
        } else {
            if (!$request->devEUI || !$request->UID) {
                return back()->withErrors(['capteur' => 'Le devEUI et l\'UID sont tous les deux requis.'])->withInput();
            }
            $query->where('devEUI', $request->devEUI)->where('UID', $request->UID);
        }

        $capteur = $query->first();

        if (!$capteur) {
            return back()
                ->withErrors(['capteur' => 'Capteur introuvable. Assurez-vous d\'avoir entré le bon identifiant ou qu\'il a déjà émis ses premières données.'])
                ->withInput();
        }

        $capteur->update([
            'lat'            => $request->latitude,
            'long'           => $request->longitude,
            'cours_d_eau_id' => $request->cours_d_eau_id,
        ]);

        return redirect()->route('map', [
            'lat' => $request->latitude,
            'lng' => $request->longitude
        ])->with('success', 'Le capteur a été localisé et associé au cours d\'eau avec succès !');
    }

    public function syncBluetooth(Request $request)
    {
        $request->validate([
            'uid'    => ['required', 'string'],
            'lignes' => ['required', 'array', 'min:1'],
        ]);

        // firstOrCreate : si le capteur n'existe pas encore, on le crée avec l'UID.
        // lat/long/cours_d_eau_id restent null — ils seront renseignés depuis la carte.
        try {

        $capteur = Capteur::firstOrCreate(
            ['UID' => $request->uid],
        );

        // On charge les timestamps déjà présents en BDD pour ce capteur
        // afin de ne pas réinsérer des mesures existantes (distinct côté serveur).
        $existingTs = Mesure::where('capteur_id', $capteur->id)
            ->whereNotNull('date_mesure_bluetooth')
            ->pluck('date_mesure_bluetooth')
            ->mapWithKeys(fn($d) => [Carbon::parse($d)->timestamp => true])
            ->all();

        $toInsert        = [];
        $latestTs        = 0;
        $latestLigne     = null;
        $now             = now()->toDateTimeString();

        foreach ($request->lignes as $ligne) {
            $ts = (int) ($ligne['timestamp'] ?? 0);
            if ($ts <= 0) continue;
            if (isset($existingTs[$ts])) continue;

            $date = Carbon::createFromTimestamp($ts)->toDateTimeString();

            $toInsert[] = [
                'capteur_id'            => $capteur->id,
                'turbidite'             => isset($ligne['turbidite'])    ? (float) $ligne['turbidite']    : null,
                'conductivite'          => isset($ligne['conductivite']) ? (float) $ligne['conductivite'] : null,
                'temp_eau'              => isset($ligne['temp_eau'])     ? (float) $ligne['temp_eau']     : null,
                'hauteur'               => isset($ligne['hauteur'])      ? (float) $ligne['hauteur']      : null,
                'debit'                 => isset($ligne['debit'])        ? (float) $ligne['debit']        : null,
                'quali_air'             => null,
                'date_mesure_bluetooth' => $date,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];

            if ($ts > $latestTs) {
                $latestTs    = $ts;
                $latestLigne = $ligne;
            }
        }

        // Insert par chunks de 500 pour éviter les timeouts sur de grands volumes
        foreach (array_chunk($toInsert, 500) as $chunk) {
            Mesure::insert($chunk);
        }

        // Mise à jour du capteur avec les valeurs de la mesure la plus récente
        if ($latestLigne) {
            $capteur->update([
                'turbidite'             => isset($latestLigne['turbidite'])    ? (float) $latestLigne['turbidite']    : $capteur->getRawOriginal('turbidite'),
                'conductivite'          => isset($latestLigne['conductivite']) ? (float) $latestLigne['conductivite'] : $capteur->getRawOriginal('conductivite'),
                'temp_eau'              => isset($latestLigne['temp_eau'])     ? (float) $latestLigne['temp_eau']     : $capteur->getRawOriginal('temp_eau'),
                'hauteur'               => isset($latestLigne['hauteur'])      ? (float) $latestLigne['hauteur']      : $capteur->getRawOriginal('hauteur'),
                'debit'                 => isset($latestLigne['debit'])        ? (float) $latestLigne['debit']        : $capteur->getRawOriginal('debit'),
                'date_mesure_bluetooth' => Carbon::createFromTimestamp($latestTs)->toDateTimeString(),
            ]);
        }

        return response()->json([
            'ok'      => true,
            'inseres' => count($toInsert),
            'ignores' => count($request->lignes) - count($toInsert),
        ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function export(int $id)
    {
        $mesuresRaw = \Illuminate\Support\Facades\DB::table('mesures')
            ->where('capteur_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mesures Capteur ' . $id);

        $headers = ['Date & Heure', 'Température (°C)', 'Débit (L/min)', 'Hauteur (cm)', 'Turbidité (NTU)', 'Conductivité (µS/cm)'];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');

        $row = 2;
        foreach ($mesuresRaw as $m) {
            $rawDate = $m->date_mesure_bluetooth ?: $m->created_at;
            $dateFormatted = $rawDate ? \Carbon\Carbon::parse($rawDate)->format('d/m/Y H:i:s') : '';

            $sheet->fromArray([
                $dateFormatted,
                $m->temp_eau ?? null,
                $m->debit ?? null,
                $m->hauteur ?? null,
                $m->turbidite ?? null,
                $m->conductivite ?? null
            ], NULL, 'A' . $row);

            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Capteur_' . $id . '_Toutes_Les_Mesures_' . date('Ymd_Hi') . '.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 4. Téléchargement
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
