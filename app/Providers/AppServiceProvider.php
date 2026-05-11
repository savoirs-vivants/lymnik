<?php

namespace App\Providers;

use App\Models\Analyse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Gate admin : seuls les users avec role = 'admin'
        Gate::define('admin', fn (User $user) => $user->role === 'admin');

        // Le badge analyses invalides n'est visible que pour les admins
        View::composer('desktop.partials._header', function ($view) {
            $count = 0;
            if (Auth::check() && Auth::user()->role === 'admin') {
                $count = Analyse::where('est_valide', false)->count();
            }
            $view->with('invalidAnalysesCount', $count);
        });
    }
}
