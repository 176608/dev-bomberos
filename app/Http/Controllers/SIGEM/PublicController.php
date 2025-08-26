<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\SIGEM\Controller; // NOTA: Controller siempre debe ser esta dirección
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\Catalogo;
use App\Models\SIGEM\CuadroEstadistico;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la sección solicitada o usar 'inicio' como predeterminada
        $section = $request->query('section', 'inicio');
        
        // Datos iniciales para todas las vistas
        $viewData = [
            'section' => $section,
        ];
        
        // Si se solicita la sección de estadística, obtener los temas
        if ($section === 'estadistica') {
            $viewData['temas'] = Tema::orderBy('orden_indice')->get();
        }

        // Si se solicita un cuadro específico
        if ($section === 'estadistica' && $request->has('cuadro_id')) {
            $cuadro_id = $request->query('cuadro_id');
            $viewData['cuadro_id'] = $cuadro_id;
            $viewData['modo_vista'] = 'desde_catalogo';
            
            // Cargar los datos del cuadro
            $cuadro = CuadroEstadistico::with(['subtema.tema'])->find($cuadro_id);
            if ($cuadro) {
                $viewData['cuadro_data'] = $cuadro;
                $viewData['tema_seleccionado'] = $cuadro->subtema->tema;
            }
        }
        
        // Devolver la vista con los datos
        return view('roles.sigem', $viewData);
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
                'message' => 'Error catalogo: ' . $e->getMessage(),
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
     * NUEVA FUNCIÓN: Cargar partial específico
     */
    public function loadPartial($section)
    {
        // Añadir 'consulta-express' a las secciones válidas
        $validSections = ['inicio', 'catalogo', 'estadistica', 'cartografia', 'productos', 'consulta-express'];
        
        if (!in_array($section, $validSections)) {
            return response()->view('partials.inicio');
        }
        
        try {
            // Manejo especial para consulta-express
            if ($section === 'consulta-express') {
                return response()->view('partials.inicio_consulta_express');
            }
            
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
                        'excel_file' => $cuadro->excel_file,
                        'pdf_file' => $cuadro->pdf_file,
                        'excel_formated_file' => $cuadro->excel_formated_file,
                        'permite_grafica' => $cuadro->permite_grafica,
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
     * Vista estadística por tema con subtemas laterales
     */
    public function verEstadisticasPorTema($tema_id)
    {
        try {
            // Obtener el tema con sus subtemas
            $tema = Tema::with(['subtemas' => function($query) {
                $query->orderBy('orden_indice', 'asc');
            }])->findOrFail($tema_id);
            
            // Obtener todos los subtemas del tema para la navegación lateral
            $tema_subtemas = $tema->subtemas;
            
            // Obtener todos los temas para el selector principal
            $temas = Tema::orderBy('orden_indice')->get();
            
            // Si el tema tiene subtemas, obtener cuadros del primer subtema
            $cuadros = collect([]);
            $subtema_seleccionado = null;
            
            if ($tema_subtemas && $tema_subtemas->count() > 0) {
                $subtema_seleccionado = $tema_subtemas->first();
                $cuadros = CuadroEstadistico::where('subtema_id', $subtema_seleccionado->subtema_id)
                    ->orderBy('codigo_cuadro')
                    ->get();
            }
            
            return view('layouts.asigem', [
                'section' => 'estadistica',
                'tema_seleccionado' => $tema,
                'subtema_seleccionado' => $subtema_seleccionado,
                'tema_subtemas' => $tema_subtemas,
                'cuadros' => $cuadros,
                'temas' => $temas,
                'modo_vista' => 'navegacion_tema_con_subtemas',
                'current_route' => 'estadistica-por-tema'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar tema estadística:', [
                'tema_id' => $tema_id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('sigem.partial', ['section' => 'estadistica'])
                ->with('error', 'No se pudo cargar el tema seleccionado');
        }
    }

    /**
     * Obtener temas y subtemas para consulta express
     */
    public function obtenerConsultaExpressTemas()
    {
        try {
            // Consulta directa a la base de datos para simplificar y depurar
            $temas = \DB::table('consulta_express_tema')
                    ->select('ce_tema_id', 'tema')
                    ->orderBy('ce_tema_id')
                    ->get();
            
            // Agregar subtemas manualmente para cada tema
            foreach ($temas as $tema) {
                $subtemas = \DB::table('consulta_express_subtema')
                        ->select('ce_subtema_id', 'ce_subtema')
                        ->where('ce_tema_id', $tema->ce_tema_id)
                        ->orderBy('ce_subtema_id')
                        ->get();
                
                $tema->subtemas = $subtemas;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Temas cargados exitosamente',
                'temas' => $temas
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar temas de consulta express: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar temas: ' . $e->getMessage(),
                'temas' => []
            ]);
        }
    }

    /**
     * Obtener contenido de consulta express por subtema
     */
    public function obtenerConsultaExpressContenido($subtema_id)
    {
        try {
            $contenido = ce_contenido::where('ce_subtema_id', $subtema_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
        
            if (!$contenido) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró contenido para este subtema',
                    'contenido' => null
                ]);
            }
            
            // Obtener información del subtema para mostrar en la interfaz
            $subtema = ce_subtema::find($subtema_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Contenido cargado exitosamente',
                'contenido' => $contenido,
                'subtema' => $subtema,
                'actualizado' => $contenido->updated_at->format('d/m/Y H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar contenido de consulta express:', [
                'subtema_id' => $subtema_id,
                'error' => $e->getMessage()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar contenido: ' . $e->getMessage(),
                'contenido' => null
            ]);
        }
    }

    /**
     * Devuelve la vista parcial del modal de Consulta Express
     */
    public function partialConsultaExpress()
    {
        return view('partials.inicio_consulta_express');
    }

    /**
     * AJAX: Obtener subtemas por tema
     */
    public function ajaxObtenerSubtemas($tema_id)
    {
        try {
            $subtemas = \App\Models\SIGEM\ce_subtema::where('ce_tema_id', $tema_id)
                    ->orderBy('ce_subtema')
                    ->get();
        
            return response()->json([
                'success' => true,
                'subtemas' => $subtemas
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar subtemas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar subtemas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX: Obtener contenido por subtema
     */
    public function ajaxObtenerContenido($subtema_id)
    {
        try {
            $contenido = ce_contenido::where('ce_subtema_id', $subtema_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
        
            if (!$contenido) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró contenido para este subtema'
                ]);
            }
            
            // Obtener información del subtema para contexto
            $subtema = ce_subtema::with('tema')->find($subtema_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Contenido cargado exitosamente',
                'contenido' => [
                    'ce_contenido_id' => $contenido->ce_contenido_id,
                    'titulo_tabla' => $contenido->titulo_tabla,
                    'pie_tabla' => $contenido->pie_tabla,
                    'tabla_filas' => $contenido->tabla_filas,
                    'tabla_columnas' => $contenido->tabla_columnas,
                    'tabla_datos' => $contenido->tabla_datos, // Datos JSON para renderizar
                    'created_at' => $contenido->created_at,
                    'updated_at' => $contenido->updated_at
                ],
                'subtema' => $subtema ? [
                    'ce_subtema_id' => $subtema->ce_subtema_id,
                    'ce_subtema' => $subtema->ce_subtema,
                    'tema' => $subtema->tema ? [
                        'ce_tema_id' => $subtema->tema->ce_tema_id,
                        'tema' => $subtema->tema->tema
                    ] : null
                ] : null,
                'actualizado' => $contenido->updated_at ? $contenido->updated_at->format('d/m/Y H:i:s') : null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar contenido de consulta express:', [
                'subtema_id' => $subtema_id,
                'error' => $e->getMessage()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar contenido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información básica de un cuadro estadístico y su archivo Excel
     * @param int $cuadro_id ID del cuadro estadístico
     * @return \Illuminate\Http\JsonResponse
     *//*
    public function obtenerExcelCuadro($cuadro_id)
    {
        try {
            // Obtener el cuadro estadístico
            $cuadro = CuadroEstadistico::find($cuadro_id);
            
            if (!$cuadro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuadro estadístico no encontrado'
                ], 404);
            }
            
            // Información básica sobre la ruta del archivo
            $nombreArchivo = $cuadro->excel_file;
            $tieneExcel = !empty($nombreArchivo);
            
            // URL basada en asset() - usando la ruta correcta
            $urlAsset = $tieneExcel ? asset('u_excel/' . $nombreArchivo) : null;
            
            // Verificar si el archivo existe físicamente
            $rutaPublic = public_path('u_excel/' . $nombreArchivo);
            $existeEnPublic = $tieneExcel ? file_exists($rutaPublic) : false;
            
            // Devolver información del cuadro y archivo
            return response()->json([
                'success' => true,
                'cuadro' => [
                    'id' => $cuadro->cuadro_estadistico_id,
                    'codigo' => $cuadro->codigo_cuadro,
                    'titulo' => $cuadro->cuadro_estadistico_titulo,
                    'subtitulo' => $cuadro->cuadro_estadistico_subtitulo,
                    'pie_pagina' => $cuadro->pie_pagina,
                    'permite_grafica' => $cuadro->permite_grafica
                ],
                'nombre_archivo' => $nombreArchivo,
                'tiene_excel' => $tieneExcel,
                'excel_url' => $urlAsset,
                'archivo_existe' => $existeEnPublic,
                'ruta_fisica' => $rutaPublic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obtenerExcelCuadro: ' . $e->getMessage()
            ], 500);
        }
    }*/

    /**
     * Obtener información básica de un cuadro estadístico y sus archivos Excel y PDF
     * @param int $cuadro_id ID del cuadro estadístico
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerArchivosCuadro($cuadro_id)
    {
        try {
            // Obtener el cuadro estadístico
            $cuadro = CuadroEstadistico::find($cuadro_id);
            
            if (!$cuadro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuadro estadístico no encontrado'
                ], 404);
            }
            
            // Información del archivo Excel
            $nombreArchivoExcel = $cuadro->excel_file;
            $tieneExcel = !empty($nombreArchivoExcel);
            $urlExcel = $tieneExcel ? asset('u_excel/' . $nombreArchivoExcel) : null;
            $rutaExcel = public_path('u_excel/' . $nombreArchivoExcel);
            $existeExcel = $tieneExcel ? file_exists($rutaExcel) : false;
            
            // Información del archivo PDF
            $nombreArchivoPdf = $cuadro->pdf_file; // Asumiendo que tienes este campo
            $tienePdf = !empty($nombreArchivoPdf);
            $urlPdf = $tienePdf ? asset('u_pdf/' . $nombreArchivoPdf) : null; // Asumiendo carpeta u_pdf
            $rutaPdf = public_path('u_pdf/' . $nombreArchivoPdf);
            $existePdf = $tienePdf ? file_exists($rutaPdf) : false;
            
            // Información del archivo Excel Formateado
            $nombreArchivoExcelFormated = $cuadro->excel_formated_file;
            $tieneExcelFormated = !empty($nombreArchivoExcelFormated);
            $urlExcelFormated = $tieneExcelFormated ? asset('u_xlsx_formated/' . $nombreArchivoExcelFormated) : null;
            $rutaExcelFormated = public_path('u_xlsx_formated/' . $nombreArchivoExcelFormated);
            $existeExcelFormated = $tieneExcelFormated ? file_exists($rutaExcelFormated) : false;
            
            // Devolver información del cuadro y ambos archivos
            return response()->json([
                'success' => true,
                'cuadro' => [
                    'id' => $cuadro->cuadro_estadistico_id,
                    'codigo' => $cuadro->codigo_cuadro,
                    'titulo' => $cuadro->cuadro_estadistico_titulo,
                    'subtitulo' => $cuadro->cuadro_estadistico_subtitulo,
                    'pie_pagina' => $cuadro->pie_pagina,
                    'permite_grafica' => $cuadro->permite_grafica,
                    'tipo_mapa_pdf' => $cuadro->tipo_mapa_pdf //Enviamos a sigem publico nuevo campo
                ],
                'excel' => [
                    'nombre_archivo' => $nombreArchivoExcel,
                    'tiene_archivo' => $tieneExcel,
                    'url' => $urlExcel,
                    'archivo_existe' => $existeExcel,
                    'ruta_fisica' => $rutaExcel
                ],
                'pdf' => [
                    'nombre_archivo' => $nombreArchivoPdf,
                    'tiene_archivo' => $tienePdf,
                    'url' => $urlPdf,
                    'archivo_existe' => $existePdf,
                    'ruta_fisica' => $rutaPdf
                ],
                'excel_formated' => [
                    'nombre_archivo' => $nombreArchivoExcelFormated,
                    'tiene_archivo' => $tieneExcelFormated,
                    'url' => $urlExcelFormated,
                    'archivo_existe' => $existeExcelFormated,
                    'ruta_fisica' => $rutaExcelFormated
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obtenerArchivosCuadro: ' . $e->getMessage()
            ], 500);
        }
    }
}
