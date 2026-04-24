<?php

use App\Http\Controllers\AnalyseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoursDEauController;
use App\Http\Controllers\MobileController;

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
    Route::get('/mobile/analyse/create', [AnalyseController::class, 'create'])->name('mobile.analyse.create');
    Route::post('/mobile/analyse',       [AnalyseController::class, 'store'])->name('mobile.analyse.store');
    Route::get('/mobile/cours-d-eau/nearest', [CoursDEauController::class, 'nearest'])
     ->name('mobile.cours-d-eau.nearest');
    Route::get('/mobile/mes-analyses', [AnalyseController::class, 'myAnalyses']) ->name('mobile.analyses');
    Route::get('/mobile/profil', [MobileController::class, 'profil'])->name('mobile.profil');
});
