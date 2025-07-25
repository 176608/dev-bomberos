<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\TemaController;
use App\Http\Controllers\SubtemaController;
use App\Http\Controllers\GeograficoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ImagenesController;
use App\Http\Controllers\ConsultaExpressDatoController;
use App\Http\Controllers\ConsultaExpressContenidoController;
use App\Models\ConsultaExpressContenido;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (sin login)
|--------------------------------------------------------------------------
*/

// Página pública de bienvenida o redirección al panel si admin está logueado
Route::get('/', function () {
    if (Auth::check() && Auth::user()->name === 'admin') {
        return redirect()->route('subtema.index'); // Panel de administración
    }

    return view('welcome');
});

// Vista pública: sección Geográfico
Route::get('/geografico', [GeograficoController::class, 'index']);

// Mostrar contenido dinámico por subtema (llamado desde JavaScript)
Route::get('/contenido-tema', function (Request $request) {
    $subtema_id = $request->query('subtema_id');

    $contenido = ConsultaExpressContenido::where('ce_subtema_id', $subtema_id)->first();

    if (!$contenido) {
        return response('<p>No se encontró contenido para este subtema.</p>', 404);
    }

    return view('subtema.contenido', compact('contenido'));
});

// Ruta AJAX o pública para guardar cambios desde Blade (admin)
Route::put('/contenido/{id}', [ConsultaExpressContenidoController::class, 'update'])->name('contenido.update');



/*
|--------------------------------------------------------------------------
| Rutas Privadas (solo admin logueado)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'onlyadmin'])->group(function () {

    // CRUD de temas
    Route::resource('tema', TemaController::class);

    // CRUD de subtemas
    Route::resource('subtema', SubtemaController::class);

    // Gestión de usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    // Gestión de imágenes
    Route::get('/imagenes', [ImagenesController::class, 'index'])->name('imagenes.index');

    // CRUD de datos express
    Route::put('/consulta-express-dato/{id}', [ConsultaExpressDatoController::class, 'update'])->name('consulta-express-dato.update');
    Route::post('/consulta-express-dato', [ConsultaExpressDatoController::class, 'store'])->name('consulta-express-dato.store');
    Route::delete('/consulta-express-dato/{id}', [ConsultaExpressDatoController::class, 'destroy'])->name('consulta-express-dato.destroy');
});
