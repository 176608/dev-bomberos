<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\SIGEM\Controller; // NOTA: Controller siempre debe ser esta dirección
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
        $mapas = Mapa::obtenerTodos();
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_mapa',
            'mapas' => $mapas
        ]);
    }

    public function temas()
    {
        $temas = Tema::orderBy('tema_titulo', 'asc')->get(); // Corregido: tema_titulo en lugar de nombre_tema
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_tema',
            'temas' => $temas
        ]);
    }

    public function subtemas()
    {
        $subtemas = Subtema::with('tema')->orderBy('nombre_subtema', 'asc')->get();
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_subtema',
            'subtemas' => $subtemas
        ]);
    }

    public function cuadros()
    {
        $cuadros = CuadroEstadistico::with(['tema', 'subtema'])->orderBy('nombre_cuadro', 'asc')->get();
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_cuadro',
            'cuadros' => $cuadros
        ]);
    }

    public function consultas()
    {
        $temas = ce_tema::orderBy('nombre', 'asc')->get();
        $subtemas = ce_subtema::with('tema')->orderBy('nombre', 'asc')->get();
        $contenidos = ce_contenido::with(['tema', 'subtema'])->orderBy('nombre', 'asc')->get();
        
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_consultas',
            'ce_temas' => $temas,
            'ce_subtemas' => $subtemas,
            'ce_contenidos' => $contenidos
        ]);
    }
}
