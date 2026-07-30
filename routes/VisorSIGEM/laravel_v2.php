<?php

use App\Http\Controllers\VisorSIGEM\SIGEMV2Controller;
use App\Http\Controllers\VisorSIGEM\DatasetViewController;
use App\Http\Controllers\VisorSIGEM\VisorCuadroController;
use App\Http\Controllers\VisorSIGEM\DocumentoController;

Route::prefix('sigem-v2')->name('sigem.v2.')->group(function () {
    Route::get('/', [SIGEMV2Controller::class, 'index'])->name('index');
    Route::get('/catalogo', [SIGEMV2Controller::class, 'catalogo'])->name('catalogo');
    Route::get('/estadistica', [SIGEMV2Controller::class, 'estadistica'])->name('estadistica');
    Route::get('/estadistica/tema/{tema_id}', [SIGEMV2Controller::class, 'estadisticaTema'])->name('estadistica.tema');
    Route::get('/api/cuadros/{subtema_id}', [SIGEMV2Controller::class, 'ajaxCuadrosV2'])->name('api.cuadros');
    Route::get('/indicador/{id}', [SIGEMV2Controller::class, 'verCuadroRedirect'])->name('cuadro.legacy-redirect');
    Route::get('/api/indicador/{id}/datos', [SIGEMV2Controller::class, 'datosCuadroJson'])->name('api.cuadro.datos');
    Route::get('/cartografia', [SIGEMV2Controller::class, 'cartografia'])->name('cartografia');

    Route::get('/productos', [SIGEMV2Controller::class, 'productos'])->name('productos');

    Route::prefix('cuadro')->name('cuadro.')->group(function () {
        Route::get('/api/cuadro/{id}', [DatasetViewController::class, 'cuadroApi'])->name('api')->whereNumber('id');
        Route::get('/{id}', [DatasetViewController::class, 'show'])->name('show')->whereNumber('id');
    });

    Route::prefix('cuadro/{id}')->middleware(['throttle:60,1', 'log.404'])->whereNumber('id')->name('cuadro.')->group(function () {
        Route::get('/dataset', [VisorCuadroController::class, 'dataset'])->name('dataset');
        Route::get('/grafica', [VisorCuadroController::class, 'grafica'])->name('grafica');
        Route::get('/dataset/seccion/{seccion}/data', [VisorCuadroController::class, 'seccionData'])->name('seccion.data')->whereNumber('seccion');
        Route::get('/exportar/excel', [DocumentoController::class, 'exportarExcel'])->name('exportar.excel');
        Route::get('/mapa', [VisorCuadroController::class, 'mapa'])->name('mapa');
        Route::get('/mapa/descargar', [VisorCuadroController::class, 'descargarMapa'])->name('mapa.descargar');
        Route::get('/mapa/ver', [VisorCuadroController::class, 'verMapa'])->name('mapa.ver');
    });

    Route::prefix('consulta-express')->name('consulta-express.')->group(function () {
        Route::get('/', [SIGEMV2Controller::class, 'consultaExpress'])->name('index');
        Route::get('/subtemas/{tema_id}', [SIGEMV2Controller::class, 'ajaxSubtemas'])->name('subtemas');
        Route::get('/contenido/{subtema_id}', [SIGEMV2Controller::class, 'ajaxContenido'])->name('contenido');
    });
});
