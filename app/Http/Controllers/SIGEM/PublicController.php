<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\SIGEM\Controller; // NOTA: Controller siempre debe ser esta dirección
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\Catalogo;
use App\Models\SIGEM\CuadroEstadistico; // AGREGAR: Import del modelo CuadroEstadistico

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
            
            // AGREGAR: Obtener todos los cuadros estadísticos
            $cuadrosEstadisticos = CuadroEstadistico::obtenerTodos();
            
            return response()->json([
                'success' => true,
                'catalogo_estructurado' => $catalogoData['estructura'],
                'total_temas' => $catalogoData['total_temas'],
                'total_subtemas' => $catalogoData['total_subtemas'],
                'temas_detalle' => $catalogoData['temas_detalle'],
                'resumen' => $resumen,
                'cuadros_estadisticos' => $cuadrosEstadisticos, // AGREGAR: Lista de cuadros
                'total_cuadros' => $cuadrosEstadisticos->count(), // AGREGAR: Total de cuadros
                'message' => 'Catálogo cargado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el catálogo: ' . $e->getMessage(),
                'catalogo_estructurado' => [],
                'total_temas' => 0,
                'total_subtemas' => 0,
                'cuadros_estadisticos' => [], // AGREGAR: Array vacío en caso de error
                'total_cuadros' => 0 // AGREGAR: 0 en caso de error
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
    
    /**
     * NUEVA FUNCIÓN: Vista de cuadro estadístico individual
     */
    public function verCuadro(Request $request, $cuadro_id = null)
    {
        $cuadro = null;
        
        if ($cuadro_id) {
            $cuadro = CuadroEstadistico::obtenerPorId($cuadro_id);
        }
        
        return view('roles.sigem', [
            'loadPartial' => 'sigem-csv-panel',
            'cuadro' => $cuadro
        ]);
    }
    
    /**
     * NUEVA FUNCIÓN: Vista estadística sin parámetros
     */
    public function estadisticaSinParametros()
    {
        return view('roles.sigem', [
            'loadPartial' => 'sigem-csv-panel',
            'cuadro' => null
        ]);
    }

    /**
     * NUEVA FUNCIÓN: Cargar partial específico
     */
    public function loadPartial($section)
    {
        $validSections = ['inicio', 'estadistica', 'cartografia', 'productos', 'catalogo'];
        
        if (!in_array($section, $validSections)) {
            return response()->view('partials.inicio');
        }
        
        return response()->view('partials.' . $section);
    }

    /**
     * NUEVA FUNCIÓN: Vista estadística con cuadro
     */
    public function estadistica($cuadro_id = null)
    {
        return view('layouts.asigem')->with([
            'loadSection' => 'estadistica',
            'cuadro_id' => $cuadro_id
        ]);
    }
}
