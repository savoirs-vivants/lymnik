<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Ce middleware existe parce que l'application a deux systèmes d'auth distincts :
// - Auth::user()          → compte utilisateur standard (inscription/connexion)
// - session('participant') → session temporaire créée via un code de campagne
//
// Les participants (élèves) n'ont pas de compte : ils rejoignent via un code 6 lettres,
// reçoivent une session PHP et peuvent saisir des analyses sans s'inscrire.
// Laravel's 'auth' middleware ne connaît pas ce mécanisme, d'où ce middleware dédié.
class AuthOrParticipant
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() || session()->has('participant')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
