<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\CuadroEstadistico;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;

class AdminController extends Controller
{
    public function index()
    {
        // Verificar permisos de administrador
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->hasRole('Administrador') && !$user->hasRole('Desarrollador')) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder al panel SIGEM.');
        }

        return view('roles.sigem_admin');
    }

    public function mapas()
    {
        return view('roles.sigem_admin')->with('crud_view', 'SIGEM.CRUD_mapa');
    }

    public function temas()
    {
        return view('roles.sigem_admin')->with('crud_view', 'SIGEM.CRUD_tema');
    }

    public function subtemas()
    {
        return view('roles.sigem_admin')->with('crud_view', 'SIGEM.CRUD_subtema');
    }

    public function cuadros()
    {
        return view('roles.sigem_admin')->with('crud_view', 'SIGEM.CRUD_cuadro');
    }

    public function consultas()
    {
        return view('roles.sigem_admin')->with('crud_view', 'SIGEM.CRUD_consultas');
    }
}
