<?php
/* <!-- Archivo SGDictamen - Dictámenes - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\SGDictamen\DictamenController;
use Illuminate\Support\Facades\Route;

// Rutas PÚBLICAS (sin autenticación)
Route::get('/dictamenes', [DictamenController::class, 'publicIndex'])
    ->name('dictamenes.public');

// Rutas PROTEGIDAS (con autenticación) - Admin y Editor
Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])
    ->prefix('admin/dictamenes')  // ← CAMBIO: Agregamos 'admin/' al prefijo
    ->name('sg-dictamen.')
    ->group(function () {
    
    // Index: Listado - Acceso para Admin Dictamenes, Editores y Desarrollador
    Route::get('/', [DictamenController::class, 'index'])->name('index')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');
    
    // Ver detalle
    Route::get('/{dictamen}/view', [DictamenController::class, 'view'])->name('view')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');
    
    // Editar + Actualizar - Admin Dictamenes, Editores y Desarrollador
    Route::middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador')->group(function () {
        Route::get('/{dictamen}/edit', [DictamenController::class, 'edit'])->name('edit');
        Route::put('/{dictamen}', [DictamenController::class, 'update'])->name('update');
    });
    
    // Crear + Eliminar + Ver Eliminados - Admin Dictamenes y Desarrollador
Route::middleware('role:Administrador Dictamenes,Desarrollador')->group(function () {
    Route::post('/', [DictamenController::class, 'store'])->name('store');
    Route::delete('/{dictamen}', [DictamenController::class, 'destroy'])->name('destroy');
    
    // Ruta para ver el historial de eliminados
    Route::get('/deleted', [DictamenController::class, 'deletedDictamenes'])->name('deleted');
});

// Ruta para RESTAURAR un dictamen eliminado
Route::post('/deleted/{id}/restore', [DictamenController::class, 'restoreDeleted'])
    ->name('restore')
    ->middleware('role:Administrador Dictamenes,Desarrollador');

    // AJAX para DataTables
    Route::get('/data', [DictamenController::class, 'dataTable'])->name('data')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes,Desarrollador');
});