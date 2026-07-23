<?php

use App\Http\Controllers\GestorSIGEM\TemaController;
use App\Http\Controllers\GestorSIGEM\CuadroV2Controller;
use App\Http\Controllers\GestorSIGEM\ConsultaExpressController;
use App\Http\Controllers\GestorSIGEM\AdminController;
use App\Http\Controllers\GestorSIGEM\DatasetController;

Route::prefix('sgiem')->name('sgiem.')->group(function () {

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        // ============ TEMAS V2 ============
        Route::get('/temas', [TemaController::class, 'temas'])->name('temas');
        Route::post('/temas/crear', [TemaController::class, 'storeTema'])->name('temas.crear');
        Route::put('/temas/{id}/actualizar', [TemaController::class, 'updateTema'])->name('temas.actualizar');
        Route::delete('/temas/{id}/eliminar', [TemaController::class, 'destroyTema'])->name('temas.eliminar');

        // ============ SUBTEMAS V2 ============
        Route::get('/subtemas', [TemaController::class, 'subtemas'])->name('subtemas');
        Route::post('/subtemas/crear', [TemaController::class, 'storeSubtema'])->name('subtemas.crear');
        Route::put('/subtemas/{id}/actualizar', [TemaController::class, 'updateSubtema'])->name('subtemas.actualizar');
        Route::delete('/subtemas/{id}/eliminar', [TemaController::class, 'destroySubtema'])->name('subtemas.eliminar');
        Route::get('/subtemas/siguiente-orden/{tema_id}', [TemaController::class, 'siguienteOrden'])->name('subtemas.siguiente-orden');
        Route::get('/cuadros/subtemas/{tema_id}', [TemaController::class, 'subtemasPorTema']);

        // ============ CUADROS ============
        Route::prefix('cuadros')->name('cuadros.')->group(function () {
            Route::get('/', [CuadroV2Controller::class, 'index'])->name('index');
            Route::get('/crear', [CuadroV2Controller::class, 'create'])->name('create');
            Route::post('/', [CuadroV2Controller::class, 'store'])->name('store');
            Route::get('/{id}', [CuadroV2Controller::class, 'show'])->name('show');
            Route::get('/{id}/editar', [CuadroV2Controller::class, 'edit'])->name('edit');
            Route::put('/{id}', [CuadroV2Controller::class, 'update'])->name('update');
            Route::delete('/{id}', [CuadroV2Controller::class, 'destroy'])->name('destroy');
            Route::put('/{id}/toggle-publicado', [CuadroV2Controller::class, 'togglePublicado'])->name('toggle-publicado');
            Route::get('/{id}/datos', [CuadroV2Controller::class, 'datosJson'])->name('datos-json');
            Route::get('/{id}/dataset', [CuadroV2Controller::class, 'datasetManage'])->name('dataset');
            Route::prefix('{id}/dataset')->name('dataset.')->group(function () {
                Route::get('/estado', [DatasetController::class, 'estado'])->name('estado');
                Route::post('/generar', [DatasetController::class, 'generar'])->name('generar');
                Route::post('/fila', [DatasetController::class, 'storeFila'])->name('fila.store');
                Route::delete('/fila/{categoria}', [DatasetController::class, 'destroyFila'])->name('fila.destroy');
                Route::post('/columna', [DatasetController::class, 'storeColumna'])->name('columna.store');
                Route::delete('/columna/{categoria}', [DatasetController::class, 'destroyColumna'])->name('columna.destroy');
                Route::put('/celda/{dato}', [DatasetController::class, 'updateCelda'])->name('celda.update');
                Route::put('/categoria/{categoria}', [DatasetController::class, 'updateCategoria'])->name('categoria.update');
                Route::post('/paste', [DatasetController::class, 'paste'])->name('paste');
                Route::post('/paste-categorias', [DatasetController::class, 'pasteCategorias'])->name('paste-categorias');
                Route::post('/hijo', [DatasetController::class, 'storeHijo'])->name('hijo.store');
                Route::post('/clonar/{categoria}', [DatasetController::class, 'cloneCategoria'])->name('clonar');
                Route::post('/importar', [DatasetController::class, 'importar'])->name('importar');
                Route::put('/pivot', [DatasetController::class, 'updatePivot'])->name('pivot.update');
                Route::delete('/datos', [DatasetController::class, 'limpiarDatos'])->name('datos.limpiar');
                Route::post('/seccion', [DatasetController::class, 'storeSeccion'])->name('seccion.store');
                Route::put('/seccion/{seccion}', [DatasetController::class, 'updateSeccion'])->name('seccion.update');
                Route::delete('/seccion/{seccion}', [DatasetController::class, 'destroySeccion'])->name('seccion.destroy');
                Route::get('/seccion/{seccion}/data', [DatasetController::class, 'switchSeccion'])->name('seccion.switch');
                Route::post('/seccion/{seccion}/reordenar', [DatasetController::class, 'reordenarSeccion'])->name('seccion.reordenar');
            });
        });

        // ============ CONSULTA EXPRESS ============
        Route::get('/consultas', [ConsultaExpressController::class, 'index'])->name('consultas');
        Route::post('/consultas/tema/crear', [ConsultaExpressController::class, 'storeTema'])->name('consultas.tema.crear');
        Route::put('/consultas/tema/{id}/actualizar', [ConsultaExpressController::class, 'updateTema'])->name('consultas.tema.actualizar');
        Route::delete('/consultas/tema/{id}/eliminar', [ConsultaExpressController::class, 'destroyTema'])->name('consultas.tema.eliminar');
        Route::post('/consultas/contenido/crear', [ConsultaExpressController::class, 'storeContenido'])->name('consultas.contenido.crear');
        Route::put('/consultas/contenido/{id}/actualizar', [ConsultaExpressController::class, 'updateContenido'])->name('consultas.contenido.actualizar');
        Route::delete('/consultas/contenido/{id}/eliminar', [ConsultaExpressController::class, 'destroyContenido'])->name('consultas.contenido.eliminar');
        Route::get('/consultas/contenido/{id}', [ConsultaExpressController::class, 'contenido']);

        // ============ AUDITORÍA ============
        Route::get('/auditoria/{id}', [AdminController::class, 'detalleAuditoria'])->name('auditoria.detalle');
    });
});
