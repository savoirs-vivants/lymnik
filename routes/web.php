<?php

use App\Http\Controllers\BackOfficeController;
use App\Http\Controllers\AnalyseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoursDEauController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfilController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/mobile', [MobileController::class, 'index'])->name('index_mobile');

Route::get('/index_web', function () {
    return view('web/dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/analyse/create', [AnalyseController::class, 'create'])->name('analyse.create');
    Route::post('/analyse',       [AnalyseController::class, 'store'])->name('analyse.store');

    Route::get('/profil', [ProfilController::class, 'profil'])->name('profil');

    Route::get('/mobile/cours-d-eau/nearest', [CoursDEauController::class, 'nearest'])
        ->name('mobile.cours-d-eau.nearest');
    Route::get('/mobile/mes-analyses', [AnalyseController::class, 'myAnalyses'])->name('mobile.analyses');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('desktop.dashboard');

    Route::get('/backoffice',           [BackOfficeController::class, 'index'])->name('desktop.backoffice');
    Route::put('/backoffice/{user}',    [BackOfficeController::class, 'update'])->name('desktop.backoffice.update');
    Route::delete('/backoffice/users/{user}', [BackOfficeController::class, 'destroy'])->name('desktop.backoffice.destroy');

    Route::get('/map', [MapController::class, 'index'])->name('desktop.map');
});
