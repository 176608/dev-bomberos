<?php

use Illuminate\Support\Facades\Route;

// 🔐 Rutas de búsqueda con Rate Limiting (60 peticiones por minuto por IP)
Route::middleware(['throttle:60,1'])->group(function () {
    
    // Home / Página principal del catálogo
    Route::get('/biblioteca', [\App\Http\Controllers\Biblioteca\CatalogoController::class, 'publicIndex'])
        ->name('home');

    // Búsqueda simple - usa SearchController
    Route::get('/search', [\App\Http\Controllers\Biblioteca\SearchController::class, 'search'])
        ->name('search.simple');

    // Búsqueda con filtro de material
    Route::get('/biblioteca/search', [\App\Http\Controllers\Biblioteca\SearchController::class, 'search'])
        ->name('biblioteca.search');

    // Búsqueda avanzada
    Route::get('/search/advanced', [\App\Http\Controllers\Biblioteca\SearchController::class, 'advanced'])
        ->name('search.advanced');

    // Alias para compatibilidad
    Route::get('/catalogo', [\App\Http\Controllers\Biblioteca\CatalogoController::class, 'publicIndex'])
        ->name('biblioteca.public');
});

// Rutas PÚBLICAS sin límite (páginas estáticas - no consumen base de datos)
Route::get('/sobre-la-biblioteca', function() {
    return view('biblioteca.about');
})->name('about.library');