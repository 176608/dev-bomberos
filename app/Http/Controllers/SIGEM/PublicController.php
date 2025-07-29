<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\SIGEM\Controller;
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;

class PublicController extends Controller
{
    /**
     * Vista principal SIGEM
     */
    public function index()
    {
        return view('roles.sigem');
    }
    
    /**
     * Obtener mapas para cartografía (AJAX)
     * Este método ES EL QUE NECESITAMOS
     */
    public function obtenerMapas()
    {
        try {
            // Obtener todos los mapas de la base de datos
            $mapas = Mapa::obtenerTodos();
            
            return response()->json([
                'success' => true,
                'mapas' => $mapas,
                'total' => $mapas->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al obtener mapas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar mapas: ' . $e->getMessage(),
                'mapas' => []
            ], 500);
        }
    }
    
    /**
     * Obtener temas para estadísticas
     */
    public function obtenerTemas()
    {
        try {
            $temas = Tema::obtenerTodos();
            
            return response()->json([
                'success' => true,
                'temas' => $temas
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar temas'
            ], 500);
        }
    }
    
    /**
     * Obtener subtemas por tema
     */
    public function obtenerSubtemas($tema)
    {
        try {
            $subtemas = Subtema::obtenerPorTema($tema);
            
            return response()->json([
                'success' => true,
                'subtemas' => $subtemas
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar subtemas'
            ], 500);
        }
    }
    
    /**
     * Obtener datos para catálogo
     */
    public function obtenerCatalogo()
    {
        try {
            $temas = Tema::with('subtemas')->get();
            
            return response()->json([
                'success' => true,
                'catalogo' => $temas
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar catálogo'
            ], 500);
        }
    }
    
    /**
     * Vista dashboard público
     */
    public function dashboard()
    {
        return view('sigem.public.dashboard');
    }
    
    /**
     * Vista geográfica pública
     */
    public function geografico()
    {
        return view('sigem.public.geografico');
    }
    
    /**
     * Vista estadísticas públicas
     */
    public function estadisticas()
    {
        return view('sigem.public.estadisticas');
    }
}
