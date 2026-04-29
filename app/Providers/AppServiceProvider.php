<?php

namespace App\Providers;

use App\Models\Analyse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('desktop.partials._header', function ($view) {
            $count = 0;
            if (Auth::check()) {
                $count = Analyse::where('est_valide', false)->count();
            }
            $view->with('invalidAnalysesCount', $count);
        });
    }
}
