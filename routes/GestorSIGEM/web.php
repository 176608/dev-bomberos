<?php

use App\Http\Controllers\GestorSIGEM\TemaController;
use App\Http\Controllers\GestorSIGEM\CuadroV2Controller;
use App\Http\Controllers\GestorSIGEM\ConsultaExpressController;
use App\Http\Controllers\GestorSIGEM\AdminController;
use App\Http\Controllers\GestorSIGEM\DatasetController;

Route::prefix('sgiem')->name('sgiem.')->group(function () {

    Route::get('/v1/catalogo', [CuadroV2Controller::class, 'catalogoPublico'])
        ->name('v1.catalogo');

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
            Route::post('/{id}/procesar-dataset', [CuadroV2Controller::class, 'procesarDataset'])->name('procesar-dataset');
            Route::get('/{id}/interpretacion', [CuadroV2Controller::class, 'editarInterpretacion'])->name('interpretacion');
            Route::put('/{id}/categorias', [CuadroV2Controller::class, 'actualizarCategorias'])->name('actualizar-categorias');
            Route::put('/{id}/datos/{dato}', [CuadroV2Controller::class, 'actualizarDato'])->name('actualizar-dato');
            Route::post('/{id}/categorias', [CuadroV2Controller::class, 'agregarCategoria'])->name('agregar-categoria');
            Route::put('/{id}/toggle-publicado', [CuadroV2Controller::class, 'togglePublicado'])->name('toggle-publicado');
            Route::get('/{id}/datos', [CuadroV2Controller::class, 'datosJson'])->name('datos-json');
            Route::get('/{id}/dataset', [CuadroV2Controller::class, 'datasetManage'])->name('dataset');
            Route::prefix('{id}/dataset')->name('dataset.')->group(function () {
                Route::get('/estado', [DatasetController::class, 'estado'])->name('estado');
                Route::post('/generar', [DatasetController::class, 'generar'])->name('generar');

                // Tree CRUD
                Route::post('/raiz', [DatasetController::class, 'storeRaiz'])->name('raiz.store');
                Route::post('/{padre}/hijo', [DatasetController::class, 'storeHijo'])->name('hijo.store');
                Route::post('/{categoria}/hermano', [DatasetController::class, 'storeHermano'])->name('hermano.store');
                Route::delete('/categoria/{categoria}', [DatasetController::class, 'destroyCategoria'])->name('categoria.destroy');
                Route::put('/categoria/{categoria}', [DatasetController::class, 'updateCategoria'])->name('categoria.update');
                Route::put('/categoria/{categoria}/tipo', [DatasetController::class, 'updateTipoCategoria'])->name('categoria.tipo');
                Route::put('/categoria/{categoria}/reordenar', [DatasetController::class, 'reordenar'])->name('categoria.reordenar');

                // Legacy row/column (for convenience)
                Route::post('/fila', [DatasetController::class, 'storeFila'])->name('fila.store');
                Route::delete('/fila/{categoria}', [DatasetController::class, 'destroyFila'])->name('fila.destroy');
                Route::post('/columna', [DatasetController::class, 'storeColumna'])->name('columna.store');
                Route::delete('/columna/{categoria}', [DatasetController::class, 'destroyColumna'])->name('columna.destroy');

                // Cell data
                Route::put('/celda/{dato}', [DatasetController::class, 'updateCelda'])->name('celda.update');
                Route::post('/celda', [DatasetController::class, 'updateCeldaPorCruze'])->name('celda.cruze');

                // Paste / Import / Cleanup
                Route::post('/paste', [DatasetController::class, 'paste'])->name('paste');
                Route::post('/importar', [DatasetController::class, 'importar'])->name('importar');
                Route::delete('/limpiar', [DatasetController::class, 'limpiar'])->name('limpiar');
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
