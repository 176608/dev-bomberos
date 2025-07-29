<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM Laravel - NO ELIMINAR COMENTARIO --> */
// routes/SIGEM/laravel.php (nuevo archivo para evitar conflictos)
use App\Http\Controllers\SIGEM\PublicController;
use App\Http\Controllers\SIGEM\AdminController;

// Rutas públicas Laravel del módulo SIGEM
Route::prefix('sigem')->group(function () {  
    
    // Vista principal pública
    Route::get('/', [PublicController::class, 'index'])->name('sigem.laravel.public');
    
    // Vistas públicas adicionales
    Route::get('/dashboard', [PublicController::class, 'dashboard'])->name('sigem.laravel.dashboard');
    Route::get('/geografico', [PublicController::class, 'geografico'])->name('sigem.laravel.geografico');
    Route::get('/estadisticas', [PublicController::class, 'estadisticas'])->name('sigem.laravel.estadisticas');
    
    // === RUTAS AJAX PARA CONTENIDO DINÁMICO ===
    
    // Obtener mapas para cartografía (AJAX)
    Route::get('/mapas', [PublicController::class, 'obtenerMapas'])->name('sigem.laravel.mapas');
    
    // Obtener temas para estadísticas (AJAX)
    Route::get('/temas', [PublicController::class, 'obtenerTemas'])->name('sigem.laravel.temas');
    
    // Obtener subtemas por tema (AJAX)
    Route::get('/subtemas/{tema}', [PublicController::class, 'obtenerSubtemas'])->name('sigem.laravel.subtemas');
    
    // Obtener datos para catálogo (AJAX)
    Route::get('/catalogo', [PublicController::class, 'obtenerCatalogo'])->name('sigem.laravel.catalogo');
    
    // Obtener índice de cuadros estadísticos
    Route::get('/indice-cuadros', [PublicController::class, 'generarIndiceCuadros'])->name('sigem.laravel.indice.cuadros');
    
    // === RUTAS ESPECÍFICAS PARA CADA SECCIÓN ===
    
    // Sección INICIO (datos de dashboard)
    Route::get('/datos-inicio', [PublicController::class, 'obtenerDatosInicio'])->name('sigem.laravel.datos.inicio');
    
    // Sección PRODUCTOS (datos dinámicos)
    Route::get('/productos', [PublicController::class, 'obtenerProductos'])->name('sigem.laravel.productos');
    
    // === RUTAS DE COMPATIBILIDAD CON ARCHIVOS PHP ORIGINALES ===
    
    // Redireccionar a archivos PHP específicos (mantener compatibilidad)
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
    })->name('sigem.redirect.catalogo');
});

// Rutas administrativas Laravel SIGEM  
Route::prefix('sigem')->middleware(['auth'])->group(function () {  
    
    // Panel principal de administración
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('sigem.laravel.admin')
        ->middleware('role:Administrador,Desarrollador');
    
    // Gestión de contenidos administrativos
    Route::middleware('role:Administrador,Desarrollador')->group(function () {
        Route::get('/admin/temas', [AdminController::class, 'temas'])->name('sigem.laravel.admin.temas');
        Route::get('/admin/subtemas', [AdminController::class, 'subtemas'])->name('sigem.laravel.admin.subtemas');
        Route::get('/admin/contenidos', [AdminController::class, 'contenidos'])->name('sigem.laravel.admin.contenidos');
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios'])->name('sigem.laravel.admin.usuarios');
        
        // === RUTAS CRUD PARA ADMINISTRACIÓN ===
        
        // CRUD Mapas
        Route::resource('/admin/mapas', AdminController::class . '@mapas');
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

// Rutas de integración (Bridge entre sistemas)
Route::prefix('sigem-bridge')->middleware(['auth'])->group(function () {
    
    // Bridge para acceder al SIGEM original desde Laravel
    Route::get('/to-original', function() {
        return redirect('/geografico'); // Redirige al SIGEM original
    })->name('sigem.bridge.original');
    
    // Bridge para acceder a panel admin original
    Route::get('/to-admin-original', function() {
        if (!auth()->check() || (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador'))) {
            return redirect()->route('login');
        }
        return redirect()->route('subtema.index'); // Redirige al admin original
    })->name('sigem.bridge.admin.original');
});