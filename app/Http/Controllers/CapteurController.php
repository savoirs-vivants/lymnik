<?php

namespace App\Http\Controllers;

use App\Models\Capteur;
use App\Models\Mesure;

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
}
