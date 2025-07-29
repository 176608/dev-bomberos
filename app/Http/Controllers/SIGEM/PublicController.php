<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;

class PublicController extends Controller
{
    public function index()
    {
        // Vista pública SIGEM - accesible para todos (sin autenticación)
        return view('roles.sigem');
    }
    
    public function dashboard()
    {
        // Dashboard público con estadísticas básicas
        return view('sigem.public.dashboard');
    }
    
    public function geografico()
    {
        // Vista geográfica pública
        return view('sigem.public.geografico');
    }
    
    public function estadisticas()
    {
        // Vista de estadísticas públicas
        return view('sigem.public.estadisticas');
    }
    
    /**
     * Obtener mapas para cartografía (AJAX)
     * Este es el método que SÍ usamos desde JavaScript
     */
    public function obtenerMapas()
    {
        try {
            $mapas = Mapa::obtenerParaCartografia();
            
            return response()->json([
                'success' => true,
                'mapas' => $mapas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar mapas: ' . $e->getMessage()
            ], 500);
        }
    }
}
