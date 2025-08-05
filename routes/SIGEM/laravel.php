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

    // === APIS PARA CONTENIDO DINÁMICO ===
    Route::get('/datos-inicio', [PublicController::class, 'obtenerDatosInicio'])->name('sigem.inicio');
    Route::get('/catalogo', [PublicController::class, 'obtenerCatalogo'])->name('sigem.catalogo');
    Route::get('/mapas', [PublicController::class, 'obtenerMapas'])->name('sigem.mapas');

    // Rutas para Consulta Express
    /*
    Route::get('/consulta-express/temas', [PublicController::class, 'obtenerConsultaExpressTemas'])
        ->name('sigem.consulta-express.temas');
    
    // Nueva ruta directa al partial de Consulta Express
    Route::get('/consulta-express', [PublicController::class, 'loadPartial'])
        ->name('sigem.consulta-express')
        ->defaults('section', 'consulta-express');
    
    // Ruta para obtener contenido de un subtema específico
    Route::get('/consulta-express/contenido/{subtema_id}', [PublicController::class, 'obtenerConsultaExpressContenido'])
        ->name('sigem.consulta-express.contenido');*/
});

// Rutas AJAX para Consulta Express
Route::prefix('sigem/ajax')->group(function () {
    Route::get('/consulta-express/subtemas/{tema_id}', [PublicController::class, 'ajaxObtenerSubtemas']);
    Route::get('/consulta-express/contenido/{subtema_id}', [PublicController::class, 'ajaxObtenerContenido']);
});

// === MANTENER TODAS LAS RUTAS ADMINISTRATIVAS ===
Route::prefix('sigem')->middleware(['auth'])->group(function () {  
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('sigem.admin.index')
        ->middleware('role:Administrador,Desarrollador');
    
    Route::middleware('role:Administrador,Desarrollador')->group(function () {
        /*Route::get('/admin/temas', [AdminController::class, 'temas'])->name('sigem.laravel.admin.temas');
        Route::get('/admin/subtemas', [AdminController::class, 'subtemas'])->name('sigem.laravel.admin.subtemas');
        Route::get('/admin/contenidos', [AdminController::class, 'contenidos'])->name('sigem.laravel.admin.contenidos');
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios'])->name('sigem.laravel.admin.usuarios');
        */
        // === RUTAS CRUD PARA ADMINISTRACIÓN ===
        
        // CRUD Mapas
        Route::get('/admin/mapas', [AdminController::class, 'listarMapas'])->name('sigem.laravel.admin.mapas.index');
        Route::post('/admin/mapas', [AdminController::class, 'crearMapa'])->name('sigem.laravel.admin.mapas.store');
        Route::put('/admin/mapas/{id}', [AdminController::class, 'actualizarMapa'])->name('sigem.laravel.admin.mapas.update');
        Route::delete('/admin/mapas/{id}', [AdminController::class, 'eliminarMapa'])->name('sigem.laravel.admin.mapas.destroy');
        
        // CRUD Temas
        Route::get('/admin/temas/crear', [AdminController::class, 'crearTema'])->name('sigem.laravel.admin.temas.create');
        Route::post('/admin/temas', [AdminController::class, 'guardarTema'])->name('sigem.laravel.admin.temas.store');
        Route::get('/admin/temas/{id}/editar', [AdminController::class, 'editarTema'])->name('sigem.laravel.admin.temas.edit');
        Route::put('/admin/temas/{id}', [AdminController::class, 'actualizarTema'])->name('sigem.laravel.admin.temas.update');
        Route::delete('/admin/temas/{id}', [AdminController::class, 'eliminarTema'])->name('sigem.laravel.admin.temas.destroy');
        
        // CRUD Subtemas
        Route::get('/admin/subtemas/crear', [AdminController::class, 'crearSubtema'])->name('sigem.laravel.admin.subtemas.create');
        Route::post('/admin/subtemas', [AdminController::class, 'guardarSubtema'])->name('sigem.laravel.admin.subtemas.store');
        Route::get('/admin/subtemas/{id}/editar', [AdminController::class, 'editarSubtema'])->name('sigem.laravel.admin.subtemas.edit');
        Route::put('/admin/subtemas/{id}', [AdminController::class, 'actualizarSubtema'])->name('sigem.laravel.admin.subtemas.update');
        Route::delete('/admin/subtemas/{id}', [AdminController::class, 'eliminarSubtema'])->name('sigem.laravel.admin.subtemas.destroy');
    });
});
