<?php
/* <!-- Archivo VisorDictamenes - Dictámenes (nuevo frontend público) - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\VisorDictamenes\DictamenController;
use Illuminate\Support\Facades\Route;

// Ruta PÚBLICA (sin autenticación)
Route::get('/VisorDictamenes', [DictamenController::class, 'publicIndex'])
    ->middleware('throttle:60,1')
    ->name('visor-dictamenes.public');
