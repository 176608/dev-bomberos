<?php

use App\Http\Controllers\SGU\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('sgu')->name('sgu.')->group(function () {

    Route::middleware(['auth', 'role:Administrador,Desarrollador'])
        ->prefix('admin')->name('admin.')->group(function () {

        // Dashboard de Métricas (visitas y visitantes) — panel principal de SGU v2
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });
});
