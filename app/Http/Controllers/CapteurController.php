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

    public function show($id)
    {
        $capteur = Capteur::with('coursDEau')->findOrFail($id);

        $mesures = Mesure::where('capteur_id', $id)
            ->latest()
            ->take(50)
            ->get();

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
            'graphLabels',
            'graphTemp',
            'graphDebit',
            'graphHauteur',
            'graphTurbidite',
            'graphConductivite'
        ));
    }
}
