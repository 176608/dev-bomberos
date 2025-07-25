<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\Bomberos;

use App\Models\Bomberos\Colonias;
use Illuminate\Http\Request;
use App\Http\Controllers\Bomberos\Controller;

class DesarrolladorController extends Controller
{
    public function index()
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verificar que tenga el rol correcto
        if (!auth()->user()->hasRole('Desarrollador')) {
            return redirect()->route('dashboard');
        }

        // CAMBIO: Corregir la ruta de la vista
        return view('roles.desarrollador');
    }
}