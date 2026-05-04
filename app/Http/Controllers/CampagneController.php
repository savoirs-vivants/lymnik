<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampagneController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom'        => 'required|string|max:255',
            'nb_groupes' => 'required|integer|min:0|max:26',
        ]);

        $campagne = Campagne::create([
            'nom'            => $request->nom,
            'id_gestionnaire' => Auth::id(),
            'nb_groupes'     => $request->nb_groupes,
        ]);

        return response()->json([
            'code'       => $campagne->code,
            'nom'        => $campagne->nom,
            'nb_groupes' => $campagne->nb_groupes,
        ]);
    }
}
