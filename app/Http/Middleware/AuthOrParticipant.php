<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthOrParticipant
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() || session()->has('participant')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
