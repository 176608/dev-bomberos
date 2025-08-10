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
        $subtemas = Subtema::with('tema')->orderBy('subtema_titulo', 'asc')->get(); // Corregido: subtema_titulo
        $temas = Tema::orderBy('tema_titulo', 'asc')->get(); // Para el select de agregar/editar
        
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_subtema',
            'subtemas' => $subtemas,
            'temas' => $temas
        ]);
    }

    public function cuadros()
    {
        $cuadros = CuadroEstadistico::with(['subtema.tema'])->orderBy('cuadro_estadistico_titulo', 'asc')->get();
        $temas = Tema::orderBy('tema_titulo', 'asc')->get(); // Para el select de agregar/editar
        $subtemas = Subtema::with('tema')->orderBy('subtema_titulo', 'asc')->get(); // Para el select de agregar/editar
        
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_cuadro',
            'cuadros' => $cuadros,
            'temas' => $temas,
            'subtemas' => $subtemas
        ]);
    }

    public function consultas()
    {
        $ce_temas = ce_tema::obtenerTodos(); // Usar el método del modelo
        $ce_subtemas = ce_subtema::with('tema')->orderBy('ce_subtema_id', 'asc')->get(); // Corregir nombre de campo
        $ce_contenidos = ce_contenido::with(['subtema.tema'])->orderBy('created_at', 'desc')->get(); // Con relaciones
        
        return view('roles.sigem_admin')->with([
            'crud_view' => 'SIGEM.CRUD_consultas',
            'ce_temas' => $ce_temas,
            'ce_subtemas' => $ce_subtemas,
            'ce_contenidos' => $ce_contenidos
        ]);
    }
}
