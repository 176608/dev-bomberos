<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM Laravel - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\SIGEM\PublicController;
use App\Http\Controllers\SIGEM\AdminController;

// Rutas públicas Laravel del módulo SIGEM
Route::prefix('sigem')->group(function () {  
    
    // === RUTA PRINCIPAL (UNA SOLA) ===
    Route::get('/', [PublicController::class, 'index'])->name('sigem.laravel.public');
    
    // === RUTAS PARA PARTIALS (DENTRO DEL GRUPO) ===
    Route::get('/partial/{section}', [PublicController::class, 'loadPartial'])->name('sigem.laravel.partial');
    
    // === RUTAS PARA ESTADÍSTICA CON CUADRO ===
    Route::get('/estadistica/{cuadro_id?}', [PublicController::class, 'estadistica'])->name('sigem.laravel.estadistica');
    Route::get('/cuadro/{cuadro_id}', [PublicController::class, 'verCuadro'])->name('sigem.laravel.cuadro');
    Route::get('/cuadro-data/{cuadro_id}', [PublicController::class, 'obtenerCuadroData'])->name('sigem.laravel.cuadro.data');
    
    // === NUEVAS RUTAS PARA ESTADÍSTICA ===
    Route::get('/estadistica-temas', [PublicController::class, 'obtenerTemasEstadistica'])->name('sigem.estadistica.temas');
    Route::get('/estadistica-subtemas/{tema_id}', [PublicController::class, 'obtenerSubtemasEstadistica'])->name('sigem.estadistica.subtemas');
    Route::get('/estadistica-cuadros/{subtema_id}', [PublicController::class, 'obtenerCuadrosEstadistica'])->name('sigem.estadistica.cuadros');
    Route::get('/estadistica-subtema-info/{subtema_id}', [PublicController::class, 'obtenerInfoSubtema'])->name('sigem.estadistica.subtema.info');
    
    // === RUTAS AJAX PARA CONTENIDO DINÁMICO ===
    Route::get('/catalogo', [PublicController::class, 'obtenerCatalogo'])->name('sigem.laravel.catalogo');
    Route::get('/mapas', [PublicController::class, 'obtenerMapas'])->name('sigem.laravel.mapas');
    Route::get('/temas', [PublicController::class, 'obtenerTemas'])->name('sigem.laravel.temas');
    Route::get('/subtemas/{tema}', [PublicController::class, 'obtenerSubtemas'])->name('sigem.laravel.subtemas');
    Route::get('/indice-cuadros', [PublicController::class, 'generarIndiceCuadros'])->name('sigem.laravel.indice.cuadros');
    
    // === RUTAS ESPECÍFICAS PARA CADA SECCIÓN ===
    Route::get('/datos-inicio', [PublicController::class, 'obtenerDatosInicio'])->name('sigem.laravel.datos.inicio');
    Route::get('/productos', [PublicController::class, 'obtenerProductos'])->name('sigem.laravel.productos');
    /*
    // Vistas adicionales (mantener compatibilidad)
    Route::get('/geografico', [PublicController::class, 'geografico'])->name('sigem.laravel.geografico');
    
    // === RUTAS DE COMPATIBILIDAD CON ARCHIVOS PHP ORIGINALES ===
    Route::get('/redirect/geografico', function() {
        return redirect('/public/vistas_SIGEM/geografico.php');
    })->name('sigem.redirect.geografico');
    
    Route::get('/redirect/medioambiente', function() {
        return redirect('/public/vistas_SIGEM/medioambiente.php');
    })->name('sigem.redirect.medioambiente');
    
    Route::get('/redirect/sociodemografico', function() {
        return redirect('/public/vistas_SIGEM/sociodemografico.php');
    })->name('sigem.redirect.sociodemografico');
    
    Route::get('/redirect/inventariourbano', function() {
        return redirect('/public/vistas_SIGEM/inventariourbano.php');
    })->name('sigem.redirect.inventariourbano');
    
    Route::get('/redirect/economico', function() {
        return redirect('/public/vistas_SIGEM/economico.php');
    })->name('sigem.redirect.economico');
    
    Route::get('/redirect/sectorpublico', function() {
        return redirect('/public/vistas_SIGEM/sectorpublico.php');
    })->name('sigem.redirect.sectorpublico');
    
    Route::get('/redirect/catalogo', function() {
        return redirect('/public/vistas_SIGEM/catalogo.php');
    })->name('sigem.redirect.catalogo');*/
    
    // Ruta para obtener subtemas de un tema específico
    Route::get('/subtemas-estadistica/{tema_id}', [PublicController::class, 'obtenerSubtemasEstadistica']);
    Route::get('/estadistica-tema/{tema_id}', [PublicController::class, 'verEstadisticaTema'])->name('sigem.laravel.estadistica.tema');
    // Añadir esta ruta dentro del grupo de rutas de estadística
    Route::get('/estadistica-subtema/{subtema_id}', [PublicController::class, 'verEstadisticaSubtema'])->name('sigem.laravel.estadistica.subtema');
});

// === MANTENER TODAS LAS RUTAS ADMINISTRATIVAS ===
Route::prefix('sigem')->middleware(['auth'])->group(function () {  
    
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('sigem.laravel.admin')
        ->middleware('role:Administrador,Desarrollador');
    
    Route::middleware('role:Administrador,Desarrollador')->group(function () {
        Route::get('/admin/temas', [AdminController::class, 'temas'])->name('sigem.laravel.admin.temas');
        Route::get('/admin/subtemas', [AdminController::class, 'subtemas'])->name('sigem.laravel.admin.subtemas');
        Route::get('/admin/contenidos', [AdminController::class, 'contenidos'])->name('sigem.laravel.admin.contenidos');
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios'])->name('sigem.laravel.admin.usuarios');
        
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
