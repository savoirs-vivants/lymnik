<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Capteur;
use App\Models\CoursDEau;
use App\Models\Mesure;
use App\Models\Point;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $isAdmin = $user->role === 'admin';

        if ($isAdmin) {
            $totalUsers    = User::count();
            $totalAnalyses = Analyse::count();
            $totalPoints   = Point::count();
            $totalCapteurs = Capteur::count();
            $analyseBase   = Analyse::query();
        } else {
            $totalUsers    = null;
            $totalAnalyses = Analyse::where('user_id', $user->id)->count();
            $totalPoints   = Point::whereHas('analyses', fn($q) => $q->where('user_id', $user->id))->count();
            $totalCapteurs = null;
            $analyseBase   = Analyse::where('user_id', $user->id);
        }

        $qualiteData = (clone $analyseBase)
            ->whereNotNull('qualite')
            ->selectRaw('qualite, count(*) as total')
            ->groupBy('qualite')
            ->pluck('total', 'qualite');

        $totalA    = (clone $analyseBase)->count();
        $validees  = (clone $analyseBase)->where('est_valide', true)->count();

        $typeData = (clone $analyseBase)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $dernieresAnalyses = (clone $analyseBase)
            ->with(['point.coursDEau', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        $dernieresMesures = $isAdmin
            ? Mesure::with('capteur')->latest()->limit(5)->get()
            : collect();

        $moyennes = null;
        if ($isAdmin) {
            $moyennes = Mesure::latest()->limit(30)->get()->pipe(function ($mesures) {
                if ($mesures->isEmpty()) return null;
                return [
                    'turbidite'    => round($mesures->avg('turbidite'), 2),
                    'conductivite' => round($mesures->avg('conductivite'), 2),
                    'temp_eau'     => round($mesures->avg('temp_eau'), 2),
                    'hauteur'      => round($mesures->avg('hauteur'), 2),
                    'debit'        => round($mesures->avg('debit'), 2),
                ];
            });
        }

        return view('desktop.dashboard', compact(
            'isAdmin',
            'totalUsers',
            'totalAnalyses',
            'totalPoints',
            'totalCapteurs',
            'qualiteData',
            'validees',
            'totalA',
            'typeData',
            'dernieresAnalyses',
            'dernieresMesures',
            'moyennes',
        ));
    }
}
