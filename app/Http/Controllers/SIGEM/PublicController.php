<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\Controller; // CAMBIO: Controller base de Laravel
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\Catalogo; // AGREGAR: Import del modelo Catalogo

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
     * Obtener mapas para AJAX - ACTUALIZADO
     */
    public function obtenerMapas()
    {
        try {
            $mapas = Mapa::obtenerParaCartografia();
            
            return response()->json([
                'success' => true,
                'mapas' => $mapas,
                'total_mapas' => $mapas->count(),
                'message' => 'Mapas cargados exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mapas' => [],
                'total_mapas' => 0,
                'message' => 'Error al cargar mapas: ' . $e->getMessage()
            ]);
        }
    }
    
    public function obtenerCatalogo()
    {
        try {
            // Obtener estructura completa del catálogo
            $catalogoData = Catalogo::obtenerEstructuraCatalogo();
            
            // Obtener resumen estadístico
            $resumen = Catalogo::obtenerResumen();
            
            return response()->json([
                'success' => true,
                'catalogo_estructurado' => $catalogoData['estructura'],
                'total_temas' => $catalogoData['total_temas'],
                'total_subtemas' => $catalogoData['total_subtemas'],
                'temas_detalle' => $catalogoData['temas_detalle'],
                'resumen' => $resumen,
                'message' => 'Catálogo cargado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el catálogo: ' . $e->getMessage(),
                'catalogo_estructurado' => [],
                'total_temas' => 0,
                'total_subtemas' => 0
            ]);
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
