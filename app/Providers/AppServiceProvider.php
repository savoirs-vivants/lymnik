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
        Gate::define('admin', fn (User $user) => $user->role === 'admin');

        // View composer plutôt qu'une variable injectée dans chaque controller : le badge
        // "analyses invalides" doit s'afficher dans le header sur toutes les pages desktop,
        // peu importe le controller actif. L'injecter au cas par cas serait fragile
        // (oubli garanti sur chaque nouvelle route). On évite la requête pour les non-admins
        // car ils n'ont pas accès à la validation et le badge ne leur est pas montré.
        View::composer('desktop.partials._header', function ($view) {
            $count = 0;
            if (Auth::check() && Auth::user()->role === 'admin') {
                $count = Analyse::where('est_valide', false)->count();
            }
            $view->with('invalidAnalysesCount', $count);
        });
    }
}
