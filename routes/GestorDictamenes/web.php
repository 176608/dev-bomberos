<?php
/* <!-- Archivo GestorDictamenes - Dictámenes (nuevo backend) - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\GestorDictamenes\DictamenController;
use Illuminate\Support\Facades\Route;

// Rutas PROTEGIDAS (con autenticación) - Admin Dictamenes, Editor y Desarrollador
Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
    ->prefix('admin/GestorDictamenes')
    ->name('gestor-dictamenes.')
    ->group(function () {

    // Index: Listado - Acceso para Admin Dictamenes, Editores y Desarrollador
    Route::get('/', [DictamenController::class, 'index'])->name('index')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');

    // Crear - Admin Dictamenes y Desarrollador
    Route::post('/', [DictamenController::class, 'store'])->name('store')
        ->middleware('role:Administrador Dictamenes,Desarrollador');

    // Actualizar - Admin Dictamenes, Editores y Desarrollador
    Route::put('/{dictamen}', [DictamenController::class, 'update'])->name('update')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');

    // Gestión de archivos (disco storage/app/dictamenes)
    Route::get('/archivos', [DictamenController::class, 'archivosIndex'])->name('archivos')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');

    Route::post('/archivos/subir', [DictamenController::class, 'archivoSubir'])->name('archivo-subir')
        ->middleware('role:Administrador Dictamenes,Desarrollador');

    Route::get('/archivos/descargar', [DictamenController::class, 'archivoDescargar'])->name('archivo-descargar')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');

    Route::post('/archivos/vincular', [DictamenController::class, 'vincularArchivo'])->name('archivo-vincular')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');

    Route::post('/archivos/desvincular', [DictamenController::class, 'desvincularArchivo'])->name('archivo-desvincular')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');

    Route::post('/archivos/eliminar', [DictamenController::class, 'archivoEliminar'])->name('archivo-eliminar')
        ->middleware('role:Administrador Dictamenes,Desarrollador');

    // Deshabilitar (soft delete por estatus) + Ver Deshabilitados + Restaurar - Admin Dictamenes y Desarrollador
    Route::middleware('role:Administrador Dictamenes,Desarrollador')->group(function () {
        Route::delete('/{dictamen}', [DictamenController::class, 'destroy'])->name('destroy');
        Route::get('/deleted', [DictamenController::class, 'deletedDictamenes'])->name('deleted');
        Route::post('/{dictamen}/restore', [DictamenController::class, 'restore'])->name('restore');
    });

    // Historial de cambios - Admin Dictamenes, Editores y Desarrollador
    Route::get('/historial', [DictamenController::class, 'historialCambios'])->name('historial')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');
});
