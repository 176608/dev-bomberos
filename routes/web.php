<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuxController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/aux', [AuxController::class, 'index'])->name('aux');

// Agrega aquí más rutas según necesites
