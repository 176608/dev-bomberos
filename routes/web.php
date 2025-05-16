<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuxController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas con autenticación y rol
Route::prefix('admin')->middleware(['auth', 'admin.dev'])->group(function () {
    Route::get('/aux', [AuxController::class, 'index'])->name('aux');
});


