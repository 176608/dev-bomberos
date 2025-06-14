<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesarrolladorController;
use App\Http\Controllers\CapturistaController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rutas de autenticación
Route::middleware('web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Rutas por rol
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.panel');
    Route::get('/desarrollador', [DesarrolladorController::class, 'index'])
        ->name('dev.panel');
    Route::get('/capturista', [CapturistaController::class, 'index'])
        ->name('capturista.panel');

    // Admin CRUD routes
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

    // Rutas de hidrantes
    Route::get('/hidrantes/create', [CapturistaController::class, 'create'])->name('hidrantes.create');
    Route::post('/hidrantes', [CapturistaController::class, 'store'])->name('hidrantes.store');
    Route::put('/hidrantes/{hidrante}', [CapturistaController::class, 'update'])->name('hidrantes.update');
    Route::get('/hidrantes/{hidrante}/edit', [CapturistaController::class, 'edit'])->name('hidrantes.edit');

    // Rutas de configuración
    Route::prefix('configuracion')->group(function () {
        Route::get('/get', [CapturistaController::class, 'getConfiguracion'])->name('configuracion.get');
        Route::post('/save', [CapturistaController::class, 'guardarConfiguracion'])->name('configuracion.save');
    });
});


