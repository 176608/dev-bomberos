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

    // Eliminar + Ver Eliminados + Restaurar - Admin Dictamenes y Desarrollador
    Route::middleware('role:Administrador Dictamenes,Desarrollador')->group(function () {
        Route::delete('/{dictamen}', [DictamenController::class, 'destroy'])->name('destroy');
        Route::get('/deleted', [DictamenController::class, 'deletedDictamenes'])->name('deleted');
        Route::post('/deleted/{id}/restore', [DictamenController::class, 'restoreDeleted'])->name('restore');
    });
});
