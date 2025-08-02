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
            // === DATOS RAW SIMPLES ===
            
            // 1. Datos del modelo Catalogo
            $catalogoData = Catalogo::obtenerEstructuraCatalogo();
            
            // 2. Datos del modelo CuadroEstadistico
            $cuadrosEstadisticos = CuadroEstadistico::obtenerTodos();
            
            // 3. Estructura simple para debug
            $datosRaw = [
                'success' => true,
                'message' => 'Datos raw del catálogo',
                
                'temas_detalle' => $catalogoData['temas_detalle'] ?? [],
                'catalogo_estructurado' => $catalogoData['estructura'] ?? [],
                'cuadros_estadisticos' => $cuadrosEstadisticos,
            ];
            
            // LOG SIMPLE
            \Log::info('CATALOGO RAW DATA:', $datosRaw);
            
            return response()->json($datosRaw);
            
        } catch (\Exception $e) {
            \Log::error('ERROR CATALOGO:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'catalogo_modelo' => null,
                'cuadros_modelo' => [],
                'total_temas' => 0,
                'total_subtemas' => 0,
                'total_cuadros' => 0,
                'temas_detalle' => [],
                'cuadros_estadisticos' => [],
                'catalogo_estructurado' => [],
                'resumen' => []
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
        $validSections = ['inicio', 'catalogo', 'estadistica', 'cartografia', 'productos'];
        
        if (!in_array($section, $validSections)) {
            return response()->view('partials.inicio');
        }
        
        try {
            if ($section === 'estadistica') {
                // Obtener todos los temas para el selector
                $temas = Tema::orderBy('orden_indice')->get();
                
                // Detectar si viene con cuadro_id desde catálogo
                $cuadroId = request()->get('cuadro_id');
                $temaSeleccionado = null;
                $cuadroData = null;
                $modoVista = 'navegacion'; // Por defecto
                
                if ($cuadroId) {
                    $cuadroData = CuadroEstadistico::with(['subtema.tema'])->find($cuadroId);
                    if ($cuadroData && $cuadroData->subtema && $cuadroData->subtema->tema) {
                        $temaSeleccionado = $cuadroData->subtema->tema->tema_id;
                        $modoVista = 'desde_catalogo';
                    }
                }
                
                return response()->view('partials.' . $section, [
                    'temas' => $temas,
                    'cuadro_id' => $cuadroId,
                    'tema_seleccionado' => $temaSeleccionado,
                    'cuadro_data' => $cuadroData,
                    'modo_vista' => $modoVista
                ]);
            }
            
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
     * NUEVA FUNCIÓN: Obtener datos para la sección inicio
     */
    public function obtenerDatosInicio()
    {
        try {
            // Estadísticas básicas para la sección inicio
            $totalTemas = Catalogo::count();
            $totalSubtemas = Subtema::count();
            $totalCuadros = CuadroEstadistico::count();
            
            
            return response()->json([
                'success' => true,
                'message' => 'Datos de inicio cargados exitosamente',
                'estadisticas' => [
                    'total_temas' => $totalTemas,
                    'total_subtemas' => $totalSubtemas,
                    'total_cuadros' => $totalCuadros
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos de inicio: ' . $e->getMessage(),
                'estadisticas' => [],
                'cuadros_recientes' => [],
                'mapas_destacados' => []
            ]);
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
     * FUNCIÓN AJAX: Obtener datos del cuadro (MEJORAR respuesta)
     */
    public function obtenerCuadroData($cuadro_id)
    {
        try {
            $cuadro = CuadroEstadistico::with(['subtema.tema'])->find($cuadro_id);
            
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
                'subtema_info' => $cuadro->subtema ?? null,
                'debug_info' => [
                    'cuadro_id' => $cuadro_id,
                    'timestamp' => now()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar cuadro: ' . $e->getMessage(),
                'cuadro' => null,
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
        }
    }

    /**
     * NUEVA FUNCIÓN: Obtener temas para estadística
     */
    public function obtenerTemasEstadistica()
    {
        try {
            $temas = Tema::with(['subtemas'])
                ->orderBy('orden_indice')
                ->get()
                ->map(function($tema) {
                    return [
                        'tema_id' => $tema->tema_id,
                        'tema_titulo' => $tema->tema_titulo,
                        'orden_indice' => $tema->orden_indice,
                        'subtemas_count' => $tema->subtemas->count()
                    ];
                });
            
            return response()->json([
                'success' => true,
                'temas' => $temas,
                'total_temas' => $temas->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar temas: ' . $e->getMessage(),
                'temas' => []
            ]);
        }
    }

    /**
     * NUEVA FUNCIÓN: Obtener subtemas por tema
     */
    public function obtenerSubtemasEstadistica($tema_id)
    {
        try {
            $subtemas = Subtema::with(['cuadrosEstadisticos'])
                ->where('tema_id', $tema_id)
                ->orderBy('orden_indice')
                ->get()
                ->map(function($subtema) {
                    return [
                        'subtema_id' => $subtema->subtema_id,
                        'subtema_titulo' => $subtema->subtema_titulo,
                        'orden_indice' => $subtema->orden_indice,
                        'cuadros_count' => $subtema->cuadrosEstadisticos->count()
                    ];
                });
            
            return response()->json([
                'success' => true,
                'subtemas' => $subtemas,
                'total_subtemas' => $subtemas->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar subtemas: ' . $e->getMessage(),
                'subtemas' => []
            ]);
        }
    }

    /**
     * NUEVA FUNCIÓN: Obtener cuadros por subtema
     */
    public function obtenerCuadrosEstadistica($subtema_id)
    {
        try {
            $cuadros = CuadroEstadistico::where('subtema_id', $subtema_id)
                ->orderBy('codigo_cuadro')
                ->get()
                ->map(function($cuadro) {
                    return [
                        'cuadro_estadistico_id' => $cuadro->cuadro_estadistico_id,
                        'codigo_cuadro' => $cuadro->codigo_cuadro,
                        'cuadro_estadistico_titulo' => $cuadro->cuadro_estadistico_titulo,
                        'cuadro_estadistico_subtitulo' => $cuadro->cuadro_estadistico_subtitulo
                    ];
                });
            
            return response()->json([
                'success' => true,
                'cuadros' => $cuadros,
                'total_cuadros' => $cuadros->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar cuadros: ' . $e->getMessage(),
                'cuadros' => []
            ]);
        }
    }

    /**
     * NUEVA FUNCIÓN: Obtener información completa del subtema
     */
    public function obtenerInfoSubtema($subtema_id)
    {
        try {
            $subtema = Subtema::with(['tema', 'cuadrosEstadisticos'])
                ->find($subtema_id);
            
            if (!$subtema) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subtema no encontrado'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'subtema' => [
                    'subtema_id' => $subtema->subtema_id,
                    'subtema_titulo' => $subtema->subtema_titulo,
                    'descripcion' => $subtema->descripcion,
                    'imagen' => $subtema->imagen,
                    'cuadros_count' => $subtema->cuadrosEstadisticos->count(),
                    'tema' => $subtema->tema ? [
                        'tema_id' => $subtema->tema->tema_id,
                        'tema_titulo' => $subtema->tema->tema_titulo
                    ] : null
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar información: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * NUEVA FUNCIÓN: Vista estadística por tema
     */
    public function verEstadisticaTema($tema_id)
    {
        $tema = Tema::with('subtemas')->findOrFail($tema_id);
        return view('partials.estadistica_tema', [
            'tema' => $tema
        ]);
    }
    
    /**
     * NUEVA FUNCIÓN: Vista estadística por subtema
     */
    public function verEstadisticaSubtema($subtema_id)
    {
        // Obtener el subtema con su tema
        $subtema = Subtema::with(['tema'])->findOrFail($subtema_id);
        
        // Obtener todos los subtemas del mismo tema para la navegación lateral
        $tema_subtemas = Subtema::where('tema_id', $subtema->tema_id)
            ->orderBy('orden_indice')
            ->get();
        
        // Obtener cuadros del subtema
        $cuadros = CuadroEstadistico::where('subtema_id', $subtema_id)
            ->orderBy('codigo_cuadro')
            ->get();
        
        return view('partials.estadistica_subtema', [
            'subtema' => $subtema,
            'tema_subtemas' => $tema_subtemas,
            'cuadros' => $cuadros
        ]);
    }
}
