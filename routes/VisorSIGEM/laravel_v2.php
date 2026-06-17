<?php

use App\Http\Controllers\VisorSIGEM\SIGEMV2Controller;

Route::prefix('sigem-v2')->name('sigem.v2.')->group(function () {
    Route::get('/', [SIGEMV2Controller::class, 'index'])->name('index');
    Route::get('/catalogo', [SIGEMV2Controller::class, 'catalogo'])->name('catalogo');
    Route::get('/estadistica', [SIGEMV2Controller::class, 'estadistica'])->name('estadistica');
    Route::get('/estadistica/tema/{tema_id}', [SIGEMV2Controller::class, 'estadisticaTema'])->name('estadistica.tema');
    Route::get('/indicador/{id}', [SIGEMV2Controller::class, 'verIndicador'])->name('indicador');
    Route::get('/api/indicador/{id}/datos', [SIGEMV2Controller::class, 'datosIndicadorJson'])->name('api.indicador.datos');
    Route::get('/cartografia', [SIGEMV2Controller::class, 'cartografia'])->name('cartografia');

    Route::get('/productos', [SIGEMV2Controller::class, 'productos'])->name('productos');
    Route::prefix('consulta-express')->name('consulta-express.')->group(function () {
        Route::get('/', [SIGEMV2Controller::class, 'consultaExpress'])->name('index');
        Route::get('/subtemas/{tema_id}', [SIGEMV2Controller::class, 'ajaxSubtemas'])->name('subtemas');
        Route::get('/contenido/{subtema_id}', [SIGEMV2Controller::class, 'ajaxContenido'])->name('contenido');
    });
});
