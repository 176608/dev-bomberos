<?php
/* <!-- Archivo Principal Routes - NO ELIMINAR COMENTARIO --> */

// Cargar rutas del sistema Bomberos
require __DIR__.'/Bomberos/web.php';

// Incluir rutas SIGEM
require __DIR__.'/SIGEM/laravel.php';

// Incluir rutas SGDictamen (Dictámenes)
require __DIR__.'/SGDictamen/web.php';

//  Ruta pública para dictamenes (SIN autenticación)
Route::get('/dictamenes', [\App\Http\Controllers\SGDictamen\DictamenController::class, 'publicIndex'])
    ->name('dictamenes.public');