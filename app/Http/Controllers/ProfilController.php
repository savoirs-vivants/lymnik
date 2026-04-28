<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\Point;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function profil()
    {
        $user    = Auth::user();
        $isAdmin = $user->role === 'admin';

        $stats = [
            'analyses' => Analyse::where('user_id', $user->id)->count(),
            'points'   => Point::whereHas('analyses', fn($q) => $q->where('user_id', $user->id))->count(),
        ];

        return view('profil', compact('stats', 'user', 'isAdmin'));
    }
}
