<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesarrolladorController;
use App\Http\Controllers\CapturistaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
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
    Route::get('/hidrantes/data', [CapturistaController::class, 'dataTable'])->name('hidrantes.data');
    Route::get('/hidrantes/{hidrante}/view', [CapturistaController::class, 'view'])->name('hidrantes.view');
    Route::post('/hidrantes/{hidrante}/desactivar', [CapturistaController::class, 'desactivar'])->name('hidrantes.desactivar');
    Route::post('/hidrantes/{hidrante}/activar', [CapturistaController::class, 'activar'])->name('hidrantes.activar');
    Route::get('/hidrantes/resumen', [CapturistaController::class, 'resumenHidrantes'])->name('hidrantes.resumen');

    // Rutas de configuración A
    Route::prefix('configuracion')->group(function () {
        Route::post('/save', [CapturistaController::class, 'guardarConfiguracion'])->name('configuracion.save');
        Route::get('/get', [CapturistaController::class, 'getConfiguracion'])->name('configuracion.get');
    });
    // Rutas de configuración B
    Route::get('/capturista/configuracion-modal', [CapturistaController::class, 'configuracionModal'])
        ->name('capturista.configuracion-modal')
        ->middleware('auth');

    // Para actualizar filtros
    Route::post('/configuracion/update-filtros', [CapturistaController::class, 'updateFiltros'])->name('configuracion.update-filtros');

    // Para cargar el panel auxiliar
    Route::get('/capturista/panel-auxiliar', [CapturistaController::class, 'cargarPanelAuxiliar'])->name('capturista.panel-auxiliar');

    // Nuevas rutas para obtener tipo de calle y colonia
    Route::get('/calles/{calle}/tipo', [CapturistaController::class, 'getCalleTipo']);
    Route::get('/colonias/{colonia}/tipo', [CapturistaController::class, 'getColoniaTipo']);
});

// Rutas específicas para el reseteo de contraseña
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset.form');
Route::post('/password/reset', [PasswordResetController::class, 'update'])
    ->name('password.reset.update');

// Ruta para verificar el email en el login
Route::post('/login/check-email', [LoginController::class, 'checkEmail'])->name('login.checkEmail');


