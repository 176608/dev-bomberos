<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesarrolladorController;
use App\Http\Controllers\CapturistaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

// Ruta principal - Dashboard público
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Verificar email en el login
Route::post('/login/check-email', [LoginController::class, 'checkEmail'])->name('login.checkEmail');

// Rutas de reseteo de contraseña
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset.form');
Route::post('/password/reset', [PasswordResetController::class, 'update'])
    ->name('password.reset.update');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    // Admin panel
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.panel')
        ->middleware('role:admin');
        
    // Desarrollador panel
    Route::get('/desarrollador', [DesarrolladorController::class, 'index'])
        ->name('dev.panel')
        ->middleware('role:desarrollador');
        
    // Capturista panel
    Route::get('/capturista', [CapturistaController::class, 'index'])
        ->name('capturista.panel')
        ->middleware('role:capturista');

    // Admin CRUD routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Rutas de hidrantes
    Route::middleware('role:capturista,admin')->group(function () {
        Route::get('/hidrantes/create', [CapturistaController::class, 'create'])->name('hidrantes.create');
        Route::post('/hidrantes', [CapturistaController::class, 'store'])->name('hidrantes.store');
        Route::put('/hidrantes/{hidrante}', [CapturistaController::class, 'update'])->name('hidrantes.update');
        Route::get('/hidrantes/{hidrante}/edit', [CapturistaController::class, 'edit'])->name('hidrantes.edit');
        Route::get('/hidrantes/data', [CapturistaController::class, 'dataTable'])->name('hidrantes.data');
        Route::get('/hidrantes/{hidrante}/view', [CapturistaController::class, 'view'])->name('hidrantes.view');
        Route::post('/hidrantes/{hidrante}/desactivar', [CapturistaController::class, 'desactivar'])->name('hidrantes.desactivar');
        Route::post('/hidrantes/{hidrante}/activar', [CapturistaController::class, 'activar'])->name('hidrantes.activar');
        Route::get('/hidrantes/resumen', [CapturistaController::class, 'resumenHidrantes'])->name('hidrantes.resumen');
    });

    // Rutas de configuración
    Route::prefix('configuracion')->group(function () {
        Route::post('/save', [CapturistaController::class, 'guardarConfiguracion'])->name('configuracion.save');
        Route::get('/get', [CapturistaController::class, 'getConfiguracion'])->name('configuracion.get');
        Route::get('/capturista/configuracion-modal', [CapturistaController::class, 'configuracionModal'])
            ->name('capturista.configuracion-modal');
        Route::post('/update-filtros', [CapturistaController::class, 'updateFiltros'])->name('configuracion.update-filtros');
    });

    // Para cargar el panel auxiliar
    Route::get('/capturista/panel-auxiliar', [CapturistaController::class, 'cargarPanelAuxiliar'])->name('capturista.panel-auxiliar');
});
