<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM Laravel - NO ELIMINAR COMENTARIO --> */
// routes/SIGEM/laravel.php (nuevo archivo para evitar conflictos)
use App\Http\Controllers\SIGEM\PublicController;
use App\Http\Controllers\SIGEM\AdminController;

// Rutas públicas Laravel del módulo SIGEM (con prefijo diferente)
Route::prefix('sigem-laravel')->group(function () {
    
    // Vista principal pública
    Route::get('/', [PublicController::class, 'index'])->name('sigem.laravel.public');
    
    // Vistas públicas adicionales
    Route::get('/dashboard', [PublicController::class, 'dashboard'])->name('sigem.laravel.dashboard');
    Route::get('/geografico', [PublicController::class, 'geografico'])->name('sigem.laravel.geografico');
    Route::get('/estadisticas', [PublicController::class, 'estadisticas'])->name('sigem.laravel.estadisticas');
});

// Rutas administrativas Laravel SIGEM  
Route::prefix('sigem-laravel')->middleware(['auth'])->group(function () {
    
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