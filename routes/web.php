<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesarrolladorController;
use App\Http\Controllers\CapturistaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

// Ruta raíz que redirige según autenticación
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Redirigir según el rol del usuario
        if ($user->hasRole('admin') || $user->hasRole('administrador')) {
            return redirect()->route('admin.panel');
        } elseif ($user->hasRole('capturista')) {
            return redirect()->route('capturista.panel');
        } elseif ($user->hasRole('desarrollador')) {
            return redirect()->route('dev.panel');
        }
        
        // Si no tiene rol específico, ir al dashboard
        return redirect()->route('dashboard.default');
    }
    
    // Si no está autenticado, mostrar página de inicio o redirigir al login
    return redirect()->route('login');
})->name('home');

// Ruta específica para el dashboard público
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.default');

// Manejar rutas legacy y variaciones de URL
Route::get('/bev-bomberos', function () {
    return redirect('/');
});

Route::get('/public', function () {
    return redirect('/');
});

Route::get('/index.php', function () {
    return redirect('/');
});

// Rutas de autenticación
Route::middleware('web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Rutas por rol (protegidas)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.panel')
        ->middleware('role:admin');
        
    Route::get('/desarrollador', [DesarrolladorController::class, 'index'])
        ->name('dev.panel')
        ->middleware('role:desarrollador');
        
    Route::get('/capturista', [CapturistaController::class, 'index'])
        ->name('capturista.panel')
        ->middleware('role:capturista');

    // Admin CRUD routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Rutas de hidrantes (capturista y admin)
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

// Rutas específicas para el reseteo de contraseña
Route::get('/password/reset', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset.form');
Route::post('/password/reset', [PasswordResetController::class, 'update'])
    ->name('password.reset.update');

// Ruta para verificar el email en el login
Route::post('/login/check-email', [LoginController::class, 'checkEmail'])->name('login.checkEmail');


