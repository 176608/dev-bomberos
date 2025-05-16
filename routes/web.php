<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuxController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Grupo de rutas autenticadas
Route::middleware(['auth'])->group(function () {
    Route::get('/aux', [AuxController::class, 'index'])
        ->middleware(function ($request, $next) {
            if (!auth()->check() || !in_array(auth()->user()->role, ['Administrador', 'Desarrollador'])) {
                return redirect()->route('dashboard');
            }
            return $next($request);
        })
        ->name('aux');
});


