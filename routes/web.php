<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesarrolladorController;
use App\Http\Controllers\AnalistaController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas por rol
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.panel');
    Route::get('/desarrollador', [DesarrolladorController::class, 'index'])
        ->name('dev.panel');
    Route::get('/analista', [AnalistaController::class, 'index'])
        ->name('analista.panel');

    // Admin CRUD routes
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

    // Rutas de hidrantes
    Route::post('/hidrantes', [AnalistaController::class, 'store'])->name('hidrantes.store');
    // maybe this sobra
    Route::put('/hidrantes/{hidrante}', [AnalistaController::class, 'update'])->name('hidrantes.update');
    Route::get('/hidrantes/{hidrante}/edit', [AnalistaController::class, 'edit'])
        ->name('hidrantes.edit')
        ->middleware('auth');
});


