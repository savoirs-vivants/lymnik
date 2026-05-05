<?php

use App\Http\Controllers\BackOfficeController;
use App\Http\Controllers\AnalyseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\CapteurController;
use App\Http\Controllers\CoursDEauController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\StatistiqueController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mentions-legales', function () {
    return view('mentions-legales');
});

Route::get('/politique-de-confidentialite', function () {
    return view('politique-confidentialite');
});

Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/mobile', [MobileController::class, 'index'])->name('mobile');

// Route accessible sans auth (utilisée aussi par les participants via map.js)
Route::get('/mobile/cours-d-eau/nearest', [CoursDEauController::class, 'nearest'])->name('cours-d-eau.nearest');

// GET + POST /analyse accessibles aux utilisateurs auth ET aux participants
Route::middleware(\App\Http\Middleware\AuthOrParticipant::class)->group(function () {
    Route::get('/analyse/create', [AnalyseController::class, 'create'])->name('analyse.create');
    Route::post('/analyse',       [AnalyseController::class, 'store'])->name('analyse.store');
});

// ─── Session participant (sans compte) ────────────────────────────────────────
Route::get('/code',               [ParticipantController::class, 'showJoin'])->name('participant.join');
Route::post('/code/valider',      [ParticipantController::class, 'validateCode'])->name('participant.validateCode');
Route::post('/session/rejoindre', [ParticipantController::class, 'register'])->name('participant.register');
Route::post('/session/quitter',   [ParticipantController::class, 'logout'])->name('participant.logout');

Route::middleware(\App\Http\Middleware\ParticipantSession::class)->prefix('session')->name('participant.')->group(function () {
    Route::get('/analyses',     [ParticipantController::class, 'analyses'])->name('analyses');
    Route::get('/map',          [ParticipantController::class, 'map'])->name('map');
    Route::get('/statistiques', [ParticipantController::class, 'statistiques'])->name('statistiques');
});

// ─── Utilisateurs authentifiés ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/analyses', [AnalyseController::class, 'index'])->name('analyses.index');
    Route::get('/analyses/invalides', [AnalyseController::class, 'invalides'])->name('analyses.invalides');
    Route::patch('/analyse/{analyse}/valider', [AnalyseController::class, 'valider'])->name('analyse.valider');

    Route::get('/profil',          [ProfilController::class, 'profil'])->name('profil');
    Route::get('/profil/modifier', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil/modifier', [ProfilController::class, 'update'])->name('profil.update');

    Route::get('/mobile/mes-analyses', [AnalyseController::class, 'myAnalyses'])->name('analyses');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/campagne', [CampagneController::class, 'store'])->name('campagne.store');

    Route::get('/backoffice',                  [BackOfficeController::class, 'index'])->name('backoffice.index');
    Route::post('/backoffice/users',           [BackOfficeController::class, 'store'])->name('backoffice.store');
    Route::get('/backoffice/users/{id}',       [BackofficeController::class, 'showUser'])->name('backoffice.show');
    Route::put('/backoffice/{user}',           [BackOfficeController::class, 'update'])->name('backoffice.update');
    Route::delete('/backoffice/users/{user}',  [BackOfficeController::class, 'destroy'])->name('backoffice.destroy');

    Route::get('/map', [MapController::class, 'index'])->name('map');

    Route::get('/capteurs', [CapteurController::class, 'index'])->name('capteurs.index');
    Route::get('/capteurs/{id}', [CapteurController::class, 'show'])->name('capteurs.show');

    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');
    Route::post('/statistiques/export', [StatistiqueController::class, 'export'])->name('statistiques.export');
});
