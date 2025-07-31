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
     * Obtener mapas para AJAX - CON DEBUGGING
     */
    public function obtenerMapas()
    {
        try {
            $mapas = Mapa::obtenerParaCartografia();
            
            // Procesar mapas para incluir URLs completas de imágenes
            $mapasConImagenes = $mapas->map(function($mapa) {
                // Agregar URL completa de imagen si existe
                if ($mapa->icono) {
                    $mapa->imagen_url = asset('img/SIGEM_mapas/' . $mapa->icono);
                    $mapa->tiene_imagen = true;
                    
                    // DEBUG: Verificar si el archivo existe
                    $rutaCompleta = public_path('img/SIGEM_mapas/' . $mapa->icono);
                    $mapa->archivo_existe = file_exists($rutaCompleta);
                    $mapa->ruta_fisica = $rutaCompleta;
                } else {
                    $mapa->imagen_url = null;
                    $mapa->tiene_imagen = false;
                    $mapa->archivo_existe = false;
                    $mapa->ruta_fisica = null;
                }
                
                return $mapa;
            });
            
            return response()->json([
                'success' => true,
                'mapas' => $mapasConImagenes,
                'total_mapas' => $mapasConImagenes->count(),
                'message' => 'Mapas cargados exitosamente',
                // DEBUG: Información adicional
                'debug_info' => [
                    'public_path' => public_path(),
                    'asset_url' => asset(''),
                    'carpeta_imagenes' => public_path('img/SIGEM_mapas/'),
                    'carpeta_existe' => is_dir(public_path('img/SIGEM_mapas/'))
                ]
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
            // 1. OBTENER SOLO catalogo_modelo (estructura completa)
            $catalogo_modelo = Catalogo::with(['subtemas' => function($query) {
                $query->orderBy('orden_indice');
            }])
            ->orderBy('orden_indice')
            ->get();

            // 2. OBTENER SOLO cuadros_modelo (cuadros completos)
            $cuadros_modelo = CuadroEstadistico::with([
                'subtema.tema'
            ])
            ->orderBy('cuadro_estadistico_id')
            ->get();

            // 3. CALCULAR estadísticas básicas
            $total_temas = $catalogo_modelo->count();
            $total_subtemas = $catalogo_modelo->sum(function($tema) {
                return $tema->subtemas->count();
            });
            $total_cuadros = $cuadros_modelo->count();

            // 4. RESPUESTA LIMPIA - SOLO 2 arrays
            return response()->json([
                'success' => true,
                'message' => 'Catálogo cargado exitosamente',
                
                // === DATOS PRINCIPALES (solo estos 2) ===
                'catalogo_modelo' => $catalogo_modelo,
                'cuadros_modelo' => $cuadros_modelo,
                
                // === ESTADÍSTICAS ===
                'total_temas' => $total_temas,
                'total_subtemas' => $total_subtemas,
                'total_cuadros' => $total_cuadros
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar catálogo: ' . $e->getMessage()
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
        // ACTUALIZAR: Sin 'inicio'
        $validSections = ['catalogo', 'estadistica', 'cartografia', 'productos'];
        
        if (!in_array($section, $validSections)) {
            // Por defecto cargar catálogo
            return response()->view('partials.catalogo');
        }
        
        try {
            return response()->view('partials.' . $section);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Partial no encontrado',
                'section' => $section,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * NUEVA FUNCIÓN: Vista estadística con cuadro opcional
     */
    public function estadistica($cuadro_id = null)
    {
        return view('roles.sigem', [
            'loadSection' => 'estadistica',
            'cuadro_id' => $cuadro_id
        ]);
    }

    /**
     * FUNCIÓN AJAX: Obtener datos del cuadro
     */
    public function obtenerCuadroData($cuadro_id)
    {
        try {
            $cuadro = CuadroEstadistico::obtenerPorId($cuadro_id);
            
            if (!$cuadro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuadro estadístico no encontrado',
                    'cuadro' => null
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Cuadro cargado exitosamente',
                'cuadro' => $cuadro->toArray(),
                'tema_info' => $cuadro->subtema->tema ?? null,
                'subtema_info' => $cuadro->subtema ?? null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar cuadro: ' . $e->getMessage(),
                'cuadro' => null
            ]);
        }
    }
}
