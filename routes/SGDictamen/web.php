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
    
    // Index: Listado - Acceso para Admin Dictamenes y Editores
    Route::get('/', [DictamenController::class, 'index'])->name('index')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes');
    
    // Ver detalle
    Route::get('/{dictamen}/view', [DictamenController::class, 'view'])->name('view')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes');
    
    // Editar + Actualizar - Admin Dictamenes y Editores
    Route::middleware('role:Administrador Dictamenes,Editor Dictamenes')->group(function () {
        Route::get('/{dictamen}/edit', [DictamenController::class, 'edit'])->name('edit');
        Route::put('/{dictamen}', [DictamenController::class, 'update'])->name('update');
    });
    
    // Crear + Eliminar + Ver Eliminados - Solo Admin Dictamenes
Route::middleware('role:Administrador Dictamenes')->group(function () {
    Route::post('/', [DictamenController::class, 'store'])->name('store');
    Route::delete('/{dictamen}', [DictamenController::class, 'destroy'])->name('destroy');
    
    // Ruta para ver el historial de eliminados
    Route::get('/deleted', [DictamenController::class, 'deletedDictamenes'])->name('deleted');
});

// Ruta para RESTAURAR un dictamen eliminado
Route::post('/deleted/{id}/restore', [DictamenController::class, 'restoreDeleted'])
    ->name('restore')
    ->middleware('role:Administrador Dictamenes');

    // AJAX para DataTables
    Route::get('/data', [DictamenController::class, 'dataTable'])->name('data')
        ->middleware('role:Administrador Dictamenes,Editor Dictamenes');
});