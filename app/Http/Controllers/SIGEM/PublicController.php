<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\SIGEM\Controller;
use Illuminate\Http\Request;
use App\Models\SIGEM\Mapa;
use App\Models\SIGEM\Tema;
use App\Models\SIGEM\Subtema;
use App\Models\SIGEM\CuadroEstadistico; // AGREGAR: Import del modelo

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
     * Obtener datos para catálogo (CORREGIDO)
     */
    public function obtenerCatalogo()
    {
        try {
            // Obtener todos los cuadros con solo la relación subtema
            $cuadros = CuadroEstadistico::with(['subtema'])
                        ->orderBy('codigo_cuadro', 'asc')
                        ->get();
            
            // Obtener todos los temas por separado
            $temas = Tema::all()->keyBy('nombre');
            
            // Agrupar por tema para la vista estructurada
            $catalogoEstructurado = [];
            $cuadrosFormateados = [];
            
            foreach ($cuadros as $cuadro) {
                // Obtener subtema
                $subtema = $cuadro->subtema;
                $subtema_nombre = $subtema ? $subtema->nombre_subtema : 'Sin subtema';
                
                // Obtener tema a través del campo 'tema' del subtema
                $tema_string = $subtema ? $subtema->tema : 'Sin tema';
                $tema_obj = $temas->get($tema_string);
                $tema_nombre = $tema_obj ? $tema_obj->nombre : $tema_string;
                
                // Formatear cuadro para la respuesta
                $cuadroFormateado = [
                    'cuadro_estadistico_id' => $cuadro->cuadro_estadistico_id,
                    'codigo_cuadro' => $cuadro->codigo_cuadro,
                    'cuadro_estadistico_titulo' => $cuadro->cuadro_estadistico_titulo,
                    'cuadro_estadistico_subtitulo' => $cuadro->cuadro_estadistico_subtitulo,
                    'tema' => (object)[
                        'nombre' => $tema_nombre
                    ],
                    'subtema' => (object)[
                        'nombre_subtema' => $subtema_nombre
                    ]
                ];
                
                $cuadrosFormateados[] = $cuadroFormateado;
                
                // Agrupar para estructura
                if (!isset($catalogoEstructurado[$tema_nombre])) {
                    $catalogoEstructurado[$tema_nombre] = [];
                }
                
                if (!isset($catalogoEstructurado[$tema_nombre][$subtema_nombre])) {
                    $catalogoEstructurado[$tema_nombre][$subtema_nombre] = [];
                }
                
                $catalogoEstructurado[$tema_nombre][$subtema_nombre][] = [
                    'cuadro_estadistico_id' => $cuadro->cuadro_estadistico_id,
                    'codigo_cuadro' => $cuadro->codigo_cuadro,
                    'titulo' => $cuadro->cuadro_estadistico_titulo,
                    'subtitulo' => $cuadro->cuadro_estadistico_subtitulo
                ];
            }
            
            return response()->json([
                'success' => true,
                'catalogo_estructurado' => $catalogoEstructurado,
                'cuadros_todos' => $cuadrosFormateados,
                'total_cuadros' => $cuadros->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar catálogo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar catálogo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * CORREGIR: Función para generar índice de cuadros
     */
    public function generarIndiceCuadros()
    {
        try {
            $cuadros = CuadroEstadistico::with(['tema', 'subtema'])->get();
            $indice = [];

            foreach ($cuadros as $cuadro) {
                $indice[] = [
                    'cuadro_estadistico_id' => $cuadro->cuadro_estadistico_id,
                    'subtema_id' => $cuadro->subtema_id,
                    'subtema_titulo' => $cuadro->subtema->nombre_subtema ?? '', // CORREGIR campo
                    'tema_titulo' => $cuadro->tema->nombre ?? '', // CORREGIR campo
                    'codigo_cuadro' => $cuadro->codigo_cuadro,
                    'cuadro_estadistico_titulo' => $cuadro->cuadro_estadistico_titulo,
                    'cuadro_estadistico_subtitulo' => $cuadro->cuadro_estadistico_subtitulo
                ];
            }

            return response()->json([
                'success' => true,
                'indice' => $indice,
                'total' => count($indice)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar índice: ' . $e->getMessage()
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
