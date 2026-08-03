<?php
/* <!-- Archivo Principal Routes - NO ELIMINAR COMENTARIO --> */

// Cargar rutas del sistema Bomberos
require __DIR__.'/Bomberos/web.php';

// Incluir rutas SIGEM
require __DIR__.'/SIGEM/laravel.php';

// Incluir rutas VisorSIGEM v2
require __DIR__.'/VisorSIGEM/laravel_v2.php';

// Incluir rutas GestorSIGEM (SGIEM - administración)
require __DIR__.'/GestorSIGEM/web.php';

// Incluir rutas SGU v2 (Sistema de Gestión de Usuarios)
require __DIR__.'/SGU/web.php';

// Incluir rutas SGDictamen (Dictámenes - legacy)
require __DIR__.'/SGDictamen/web.php';

// Incluir rutas GestorDictamenes (nuevo backend SGD)
require __DIR__.'/GestorDictamenes/web.php';

// Incluir rutas VisorDictamenes (nuevo frontend público SGD)
require __DIR__.'/VisorDictamenes/web.php';

// Incluir rutas Biblioteca (Catalogo)
require __DIR__.'/Biblioteca/web.php';

//  Ruta pública para dictamenes (SIN autenticación)
Route::get('/dictamenes', [\App\Http\Controllers\SGDictamen\DictamenController::class, 'publicIndex'])
    ->name('dictamenes.public');