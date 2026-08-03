<?php

use App\Http\Controllers\SGU\AuditorController;
use App\Http\Controllers\SGU\DashboardController;
use App\Http\Controllers\SGU\GestorController;
use Illuminate\Support\Facades\Route;

Route::prefix('sgu')->name('sgu.')->group(function () {

    Route::middleware(['auth', 'role:Administrador,Desarrollador'])
        ->prefix('admin')->name('admin.')->group(function () {

        // Dashboard de Métricas (visitas y visitantes) — panel principal de SGU v2
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        // ============ GESTOR (Usuarios) ============
        Route::get('/gestor/usuarios', [GestorController::class, 'usuarios'])->name('gestor.usuarios');
        Route::post('/gestor/usuarios', [GestorController::class, 'store'])->name('gestor.usuarios.store');
        Route::put('/gestor/usuarios/{user}', [GestorController::class, 'update'])->name('gestor.usuarios.update');
        Route::post('/gestor/usuarios/{user}/generar-pin', [GestorController::class, 'generarPin'])
            ->name('gestor.usuarios.generar-pin');

        // ============ AUDITOR (accesos y cambios en usuarios) ============
        Route::get('/auditor/accesos', [AuditorController::class, 'accesos'])->name('auditor.accesos');
        Route::get('/auditor/usuarios', [AuditorController::class, 'usuarios'])->name('auditor.usuarios');
    });
});
