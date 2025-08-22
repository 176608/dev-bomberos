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

    // Ruta para obtener información del cuadro y su archivos Excel y PDF
    Route::get('/obtener-archivos-cuadro/{cuadro_id}', [PublicController::class, 'obtenerArchivosCuadro'])
        ->name('sigem.obtener.archivos.cuadro');

    // === APIS PARA CONTENIDO DINÁMICO ===
    Route::get('/datos-inicio', [PublicController::class, 'obtenerDatosInicio'])->name('sigem.inicio');
    Route::get('/catalogo', [PublicController::class, 'obtenerCatalogo'])->name('sigem.catalogo');
    Route::get('/mapas', [PublicController::class, 'obtenerMapas'])->name('sigem.mapas');
    
    // Rutas para Consulta Express (públicas)
    Route::prefix('consulta-express')->group(function () {
        Route::get('/temas', [PublicController::class, 'obtenerConsultaExpressTemas'])->name('sigem.consulta-express.temas');
        Route::get('/subtemas/{tema_id}', [PublicController::class, 'ajaxObtenerSubtemas'])->name('sigem.consulta-express.subtemas');
        Route::get('/contenido/{subtema_id}', [PublicController::class, 'ajaxObtenerContenido'])->name('sigem.consulta-express.contenido');
    });
});

// Rutas AJAX para Consulta Express - ADMIN
Route::middleware(['auth'])->prefix('sigem')->group(function () {
    // Rutas administrativas protegidas
    Route::get('/admin', [AdminController::class, 'index'])->name('sigem.admin.index');
    
    // Rutas GET para mostrar vistas
    Route::get('/admin/mapas', [AdminController::class, 'mapas'])->name('sigem.admin.mapas');
    Route::get('/admin/temas', [AdminController::class, 'temas'])->name('sigem.admin.temas');
    Route::get('/admin/subtemas', [AdminController::class, 'subtemas'])->name('sigem.admin.subtemas');
    Route::get('/admin/cuadros', [AdminController::class, 'cuadros'])->name('sigem.admin.cuadros');
    Route::get('/admin/consultas', [AdminController::class, 'consultas'])->name('sigem.admin.consultas');
    
    // === RUTAS POST PARA OPERACIONES CRUD ===
    // Mapas
    Route::post('/admin/mapas/crear', [AdminController::class, 'crearMapa'])->name('sigem.admin.mapas.crear');
    Route::put('/admin/mapas/{id}/actualizar', [AdminController::class, 'actualizarMapa'])->name('sigem.admin.mapas.actualizar');
    Route::delete('/admin/mapas/{id}/eliminar', [AdminController::class, 'eliminarMapa'])->name('sigem.admin.mapas.eliminar');
    
    // Temas
    Route::post('/admin/temas/crear', [AdminController::class, 'crearTema'])->name('sigem.admin.temas.crear');
    Route::put('/admin/temas/{id}/actualizar', [AdminController::class, 'actualizarTema'])->name('sigem.admin.temas.actualizar');
    Route::delete('/admin/temas/{id}/eliminar', [AdminController::class, 'eliminarTema'])->name('sigem.admin.temas.eliminar');
    
    // Subtemas
    Route::post('/admin/subtemas/crear', [AdminController::class, 'crearSubtema'])->name('sigem.admin.subtemas.crear');
    Route::put('/admin/subtemas/{id}/actualizar', [AdminController::class, 'actualizarSubtema'])->name('sigem.admin.subtemas.actualizar');
    Route::delete('/admin/subtemas/{id}/eliminar', [AdminController::class, 'eliminarSubtema'])->name('sigem.admin.subtemas.eliminar');
    
    // Cuadros
    Route::post('/admin/cuadros/crear', [AdminController::class, 'crearCuadro'])->name('sigem.admin.cuadros.crear');
    Route::put('/admin/cuadros/{id}/actualizar', [AdminController::class, 'actualizarCuadro'])->name('sigem.admin.cuadros.actualizar');
    Route::delete('/admin/cuadros/{id}/eliminar', [AdminController::class, 'eliminarCuadro'])->name('sigem.admin.cuadros.eliminar');
    Route::get('/admin/cuadros/{id}/editar', [AdminController::class, 'obtenerCuadroParaEdicion'])->name('sigem.admin.cuadros.obtener');

    // AJAX para obtener subtemas por tema
    Route::get('/admin/cuadros/subtemas/{tema_id}', [AdminController::class, 'obtenerSubtemasPorTema']);
        
    // AJAX para obtener subtemas CE por tema
    Route::get('/admin/consultas/subtemas-ce/{tema_id}', [AdminController::class, 'obtenerSubtemasCEPorTema']);
    
    // AJAX para obtener contenido CE completo
    Route::get('/admin/consultas/contenido/{id}', [AdminController::class, 'obtenerContenidoCE']);
    
    // AJAX para obtener contenido CE para edición
    Route::get('/admin/consultas/contenido/{id}/editar', [AdminController::class, 'obtenerContenidoCEParaEdicion']);

    // Consultas Express - CRUD completo
    Route::post('/admin/consultas/tema/crear', [AdminController::class, 'crearTemaCE'])->name('sigem.admin.consultas.tema.crear');
    Route::put('/admin/consultas/tema/{id}/actualizar', [AdminController::class, 'actualizarTemaCE'])->name('sigem.admin.consultas.tema.actualizar');
    Route::delete('/admin/consultas/tema/{id}/eliminar', [AdminController::class, 'eliminarTemaCE'])->name('sigem.admin.consultas.tema.eliminar');
    
    Route::post('/admin/consultas/subtema/crear', [AdminController::class, 'crearSubtemaCE'])->name('sigem.admin.consultas.subtema.crear');
    Route::put('/admin/consultas/subtema/{id}/actualizar', [AdminController::class, 'actualizarSubtemaCE'])->name('sigem.admin.consultas.subtema.actualizar');
    Route::delete('/admin/consultas/subtema/{id}/eliminar', [AdminController::class, 'eliminarSubtemaCE'])->name('sigem.admin.consultas.subtema.eliminar');
    
    Route::post('/admin/consultas/contenido/crear', [AdminController::class, 'crearContenidoCE'])->name('sigem.admin.consultas.contenido.crear');
    Route::put('/admin/consultas/contenido/{id}/actualizar', [AdminController::class, 'actualizarContenidoCE'])->name('sigem.admin.consultas.contenido.actualizar');
    Route::delete('/admin/consultas/contenido/{id}/eliminar', [AdminController::class, 'eliminarContenidoCE'])->name('sigem.admin.consultas.contenido.eliminar');
});
