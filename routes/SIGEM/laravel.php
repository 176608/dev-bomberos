<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM Laravel - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\SIGEM\PublicController;
use App\Http\Controllers\SIGEM\AdminController;

// Rutas públicas Laravel del módulo SIGEM
Route::prefix('sigem')->group(function () {  
    // === RUTA PRINCIPAL (UNA SOLA) ===
    Route::get('/', [PublicController::class, 'index'])->name('sigem.index');
    
    // === RUTAS PARA PARTIALS (CARGA AJAX) ===
    Route::get('/partial/{section}', [PublicController::class, 'loadPartial'])->name('sigem.partial');

    // === RUTA nueva de ESTADÍSTICA POR TEMA  ===
    Route::get('/estadistica-por-tema/{tema_id}', [PublicController::class, 'verEstadisticasPorTema'])
        ->name('sigem.estadistica.tema');

    // Rutas AJAX para la vista de estadística por tema
    Route::get('/obtener-cuadros-estadistica/{subtema_id}', [PublicController::class, 'obtenerCuadrosEstadistica'])
        ->name('sigem.ajax.cuadros');
    Route::get('/obtener-info-subtema/{subtema_id}', [PublicController::class, 'obtenerInfoSubtema'])
        ->name('sigem.ajax.subtema');

    // Ruta para obtener información del cuadro y su archivo Excel
    Route::get('/obtener-excel-cuadro/{cuadro_id}', [PublicController::class, 'obtenerExcelCuadro'])
        ->name('sigem.obtener.excel.cuadro');
    
    // === APIS PARA CONTENIDO DINÁMICO ===
    Route::get('/datos-inicio', [PublicController::class, 'obtenerDatosInicio'])->name('sigem.inicio');
    Route::get('/catalogo', [PublicController::class, 'obtenerCatalogo'])->name('sigem.catalogo');
    Route::get('/mapas', [PublicController::class, 'obtenerMapas'])->name('sigem.mapas');
});

// Rutas AJAX para Consulta Express
Route::prefix('sigem/ajax')->group(function () {
    Route::get('/consulta-express/subtemas/{tema_id}', [PublicController::class, 'ajaxObtenerSubtemas']);
    Route::get('/consulta-express/contenido/{subtema_id}', [PublicController::class, 'ajaxObtenerContenido']);
});

// Ruta para cargar el modal de Consulta Express directamente
Route::get('/sigem/partial/consulta-express', [PublicController::class, 'partialConsultaExpress'])
    ->name('sigem.partial.consulta-express');

// === RUTAS ADMINISTRATIVAS COMPLETAS ===
Route::prefix('sigem')->middleware(['auth', 'role:Administrador,Desarrollador'])->group(function () {  
    Route::get('/admin', [AdminController::class, 'index'])->name('sigem.admin.index');
    Route::get('/admin/mapas', [AdminController::class, 'mapas'])->name('sigem.admin.mapas');
    Route::get('/admin/temas', [AdminController::class, 'temas'])->name('sigem.admin.temas');
    Route::get('/admin/subtemas', [AdminController::class, 'subtemas'])->name('sigem.admin.subtemas');
    Route::get('/admin/cuadros', [AdminController::class, 'cuadros'])->name('sigem.admin.cuadros');
    Route::get('/admin/consultas', [AdminController::class, 'consultas'])->name('sigem.admin.consultas');
});
