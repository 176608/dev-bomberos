<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\Bomberos\DashboardController;
use App\Http\Controllers\Bomberos\AdminController;
use App\Http\Controllers\Bomberos\DesarrolladorController;
use App\Http\Controllers\Bomberos\CapturistaController;
use App\Http\Controllers\Bomberos\RegistradorController;
use App\Http\Controllers\Bomberos\Auth\LoginController;
use App\Http\Controllers\Bomberos\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

// ALTERNATIVA: Si hay conflicto, usar ruta específica
Route::get('/consultor', [DashboardController::class, 'index'])->name('consultor.dashboard');
Route::get('/consultor/buscar', [DashboardController::class, 'buscarHidrante'])->name('consultor.buscar');
Route::get('/hidrante-pdf/{id}', [DashboardController::class, 'generarPDF'])->name('hidrante.pdf');

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Rutas de registro
Route::post('/login/check-email', [LoginController::class, 'checkEmail'])->name('login.checkEmail');

// Verificar email en el login
Route::get('/check-session', function () {
    return response()->json(['autenticado' => Auth::check()]);
})->name('check.session');

// Rutas de reseteo de contraseña
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset.form');
Route::post('/password/reset', [PasswordResetController::class, 'update'])
    ->name('password.reset.update');

// Rutas protegidas por autenticación
Route::middleware([\App\Http\Middleware\PreventBackHistory::class, 'auth'])->group(function () {
    
    // Admin panel - CORREGIR NOMBRES DE ROLES
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.panel')
        ->middleware('role:Administrador,Desarrollador');
        
    // Desarrollador panel
    Route::get('/desarrollador', [DesarrolladorController::class, 'index'])
        ->name('dev.panel')
        ->middleware('role:Desarrollador');
        
    // Capturista panel - CORREGIR NOMBRES DE ROLES
    Route::get('/capturista', [CapturistaController::class, 'index'])
        ->name('capturista.panel')
        ->middleware('role:Capturista,Desarrollador');

    // Registrador panel - NUEVA RUTA
    Route::get('/registrador', [RegistradorController::class, 'index'])
        ->name('registrador.panel')
        ->middleware('role:Registrador,Desarrollador');

    // Rutas específicas del Registrador
    Route::middleware('role:Registrador,Desarrollador')->group(function () {
        // DataTables
        Route::get('/registrador/zonas/data', [RegistradorController::class, 'zonasDataTable'])->name('registrador.zonas.data');
        Route::get('/registrador/vias/data', [RegistradorController::class, 'viasDataTable'])->name('registrador.vias.data');
        
        // CRUD Zonas
        Route::post('/registrador/zonas', [RegistradorController::class, 'storeZona'])->name('registrador.zonas.store');
        Route::get('/registrador/zonas/{zona}', [RegistradorController::class, 'showZona'])->name('registrador.zonas.show');
        Route::put('/registrador/zonas/{zona}', [RegistradorController::class, 'updateZona'])->name('registrador.zonas.update');
        
        // CRUD Vías
        Route::post('/registrador/vias', [RegistradorController::class, 'storeVia'])->name('registrador.vias.store');
        Route::get('/registrador/vias/{via}', [RegistradorController::class, 'showVia'])->name('registrador.vias.show');
        Route::put('/registrador/vias/{via}', [RegistradorController::class, 'updateVia'])->name('registrador.vias.update');
    });

    // Admin CRUD routes - CORREGIR NOMBRES DE ROLES
    Route::middleware('role:Administrador,Desarrollador')->group(function () {
        Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Rutas de hidrantes - CORREGIR NOMBRES DE ROLES
    Route::middleware('role:Capturista,Administrador,Desarrollador')->group(function () {
        Route::get('/hidrantes/create', [CapturistaController::class, 'create'])->name('hidrantes.create');
        Route::post('/hidrantes', [CapturistaController::class, 'store'])->name('hidrantes.store');
        Route::put('/hidrantes/{hidrante}', [CapturistaController::class, 'update'])->name('hidrantes.update');
        Route::get('/hidrantes/{hidrante}/edit', [CapturistaController::class, 'edit'])->name('hidrantes.edit');
        Route::get('/hidrantes/data', [CapturistaController::class, 'dataTable'])->name('hidrantes.data');
        Route::get('/hidrantes/{hidrante}/view', [CapturistaController::class, 'view'])->name('hidrantes.view');
        Route::post('/hidrantes/{hidrante}/desactivar', [CapturistaController::class, 'desactivar'])->name('hidrantes.desactivar');
        Route::post('/hidrantes/{hidrante}/activar', [CapturistaController::class, 'activar'])->name('hidrantes.activar');
        Route::get('/hidrantes/resumen', [CapturistaController::class, 'resumenHidrantes'])->name('hidrantes.resumen');
        Route::get('/hidrantes/{hidrante}/historial', [CapturistaController::class, 'historialCambios'])->name('hidrantes.historial');
    });

    // Rutas de configuración - CORREGIR NOMBRES DE ROLES
    Route::prefix('configuracion')->middleware('role:Capturista,Administrador,Desarrollador')->group(function () {
        Route::post('/save', [CapturistaController::class, 'guardarConfiguracion'])->name('configuracion.save');
        Route::get('/get', [CapturistaController::class, 'getConfiguracion'])->name('configuracion.get');
        Route::get('/capturista/configuracion-modal', [CapturistaController::class, 'configuracionModal'])
            ->name('capturista.configuracion-modal');
        Route::post('/update-filtros', [CapturistaController::class, 'updateFiltros'])->name('configuracion.update-filtros');
    });

    // Para cargar el panel auxiliar - CORREGIR NOMBRES DE ROLES
    Route::get('/capturista/panel-auxiliar', [CapturistaController::class, 'cargarPanelAuxiliar'])
        ->name('capturista.panel-auxiliar')
        ->middleware('role:Capturista,Desarrollador');
});