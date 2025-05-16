<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuxController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', 'admin.dev'])->group(function () {
    Route::get('/aux', [AuxController::class, 'index'])->name('aux');
});


