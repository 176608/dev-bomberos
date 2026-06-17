<?php

use App\Http\Controllers\GestorSIGEM\AdminController;
use App\Http\Controllers\GestorSIGEM\CuadroController;

Route::prefix('sgiem')->name('sgiem.')->group(function () {

    Route::get('/v1/catalogo', [CuadroController::class, 'catalogoPublico'])
        ->name('v1.catalogo');

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        Route::get('/mapas', [AdminController::class, 'mapas'])->name('mapas');
        Route::post('/mapas/crear', [AdminController::class, 'crearMapa'])->name('mapas.crear');
        Route::put('/mapas/{id}/actualizar', [AdminController::class, 'actualizarMapa'])->name('mapas.actualizar');
        Route::delete('/mapas/{id}/eliminar', [AdminController::class, 'eliminarMapa'])->name('mapas.eliminar');

        Route::get('/temas', [AdminController::class, 'temas'])->name('temas');
        Route::post('/temas/crear', [AdminController::class, 'crearTema'])->name('temas.crear');
        Route::put('/temas/{id}/actualizar', [AdminController::class, 'actualizarTema'])->name('temas.actualizar');
        Route::delete('/temas/{id}/eliminar', [AdminController::class, 'eliminarTema'])->name('temas.eliminar');

        Route::get('/subtemas', [AdminController::class, 'subtemas'])->name('subtemas');
        Route::post('/subtemas/crear', [AdminController::class, 'crearSubtema'])->name('subtemas.crear');
        Route::put('/subtemas/{id}/actualizar', [AdminController::class, 'actualizarSubtema'])->name('subtemas.actualizar');
        Route::delete('/subtemas/{id}/eliminar', [AdminController::class, 'eliminarSubtema'])->name('subtemas.eliminar');
        Route::get('/subtemas/siguiente-orden/{tema_id}', [AdminController::class, 'obtenerSiguienteOrdenTema']);

        Route::get('/cuadros', [AdminController::class, 'cuadros'])->name('cuadros');
        Route::post('/cuadros/crear', [AdminController::class, 'crearCuadro'])->name('cuadros.crear');
        Route::put('/cuadros/{id}/actualizar', [AdminController::class, 'actualizarCuadro'])->name('cuadros.actualizar');
        Route::delete('/cuadros/{id}/eliminar', [AdminController::class, 'eliminarCuadro'])->name('cuadros.eliminar');
        Route::get('/cuadros/{id}/editar', [AdminController::class, 'obtenerCuadroParaEdicion'])->name('cuadros.obtener');
        Route::get('/cuadros/subtemas/{tema_id}', [AdminController::class, 'obtenerSubtemasPorTema']);

        Route::get('/consultas', [AdminController::class, 'consultas'])->name('consultas');
        Route::post('/consultas/tema/crear', [AdminController::class, 'crearTemaCE'])->name('consultas.tema.crear');
        Route::put('/consultas/tema/{id}/actualizar', [AdminController::class, 'actualizarTemaCE'])->name('consultas.tema.actualizar');
        Route::delete('/consultas/tema/{id}/eliminar', [AdminController::class, 'eliminarTemaCE'])->name('consultas.tema.eliminar');
        Route::post('/consultas/subtema/crear', [AdminController::class, 'crearSubtemaCE'])->name('consultas.subtema.crear');
        Route::put('/consultas/subtema/{id}/actualizar', [AdminController::class, 'actualizarSubtemaCE'])->name('consultas.subtema.actualizar');
        Route::delete('/consultas/subtema/{id}/eliminar', [AdminController::class, 'eliminarSubtemaCE'])->name('consultas.subtema.eliminar');
        Route::post('/consultas/contenido/crear', [AdminController::class, 'crearContenidoCE'])->name('consultas.contenido.crear');
        Route::put('/consultas/contenido/{id}/actualizar', [AdminController::class, 'actualizarContenidoCE'])->name('consultas.contenido.actualizar');
        Route::delete('/consultas/contenido/{id}/eliminar', [AdminController::class, 'eliminarContenidoCE'])->name('consultas.contenido.eliminar');
        Route::get('/consultas/subtemas-ce/{tema_id}', [AdminController::class, 'obtenerSubtemasCEPorTema']);
        Route::get('/consultas/contenido/{id}', [AdminController::class, 'obtenerContenidoCE']);

        Route::prefix('cuadros-v2')->name('cuadros-v2.')->group(function () {
            Route::get('/', [CuadroController::class, 'index'])->name('index');
            Route::get('/crear', [CuadroController::class, 'create'])->name('create');
            Route::post('/', [CuadroController::class, 'store'])->name('store');
            Route::get('/{id}', [CuadroController::class, 'show'])->name('show');
            Route::get('/{id}/editar', [CuadroController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CuadroController::class, 'update'])->name('update');
            Route::delete('/{id}', [CuadroController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/procesar-dataset', [CuadroController::class, 'procesarDataset'])->name('procesar-dataset');
            Route::get('/{id}/interpretacion', [CuadroController::class, 'editarInterpretacion'])->name('interpretacion');
            Route::put('/{id}/categorias', [CuadroController::class, 'actualizarCategorias'])->name('actualizar-categorias');
            Route::put('/{id}/datos/{dato}', [CuadroController::class, 'actualizarDato'])->name('actualizar-dato');
            Route::post('/{id}/categorias', [CuadroController::class, 'agregarCategoria'])->name('agregar-categoria');
        });
    });
});
