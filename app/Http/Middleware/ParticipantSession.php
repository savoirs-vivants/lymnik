<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParticipantSession
{
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('participant')) {
            return redirect()->route('participant.join')
                ->with('error', 'Veuillez rejoindre une session pour accéder à cette page.');
        }

        return $next($request);
    }
}
