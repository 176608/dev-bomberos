<?php

namespace App\Http\Controllers;

use App\Models\Hidrante;
use App\Models\Colonias;
use App\Models\Calles;
use App\Models\CatalogoCalle;
use App\Models\ConfiguracionCapturista;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CapturistaController extends Controller
{

    public function index(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'Capturista') {
            return redirect()->route('login');
        }

        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();
        $columnas = $configuracion ? $configuracion->configuracion : ConfiguracionCapturista::getDefaultConfig();
//Aca se agregan nuevos campos a las columnas
        $headerNames = [
            'fecha_inspeccion' => 'Fecha Inspección',
            'numero_estacion' => 'N° Estación',
            'calle' => 'Calle',
            'y_calle' => 'Y Calle',
            'colonia' => 'Colonia',
            'llave_fosa' => 'Llave Fosa',
            'llave_hidrante' => 'Llave Hidrante',
            'presion_agua' => 'Presión Agua',
            'estado_hidrante' => 'Estado',
            'marca' => 'Marca',
            'anio' => 'Año',
            'oficial' => 'Oficial',
            'observaciones' => 'Observaciones'
        ];

        // Si la petición es AJAX y pide solo la tabla
        if ($request->ajax() && $request->has('tabla')) {
            return view('partials.hidrantes-table', compact('columnas', 'headerNames'))->render();
        }

        return view('roles.capturista', compact('columnas', 'headerNames'));
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            $validated = $request->validate([
                'fecha_inspeccion' => 'required|date',
                'numero_estacion' => 'required|string',
                'id_calle' => 'nullable|integer',
                'id_y_calle' => 'nullable|integer',
                'id_colonia' => 'nullable|integer',
                'calle' => 'nullable|string',
                'y_calle' => 'nullable|string',
                'colonia' => 'nullable|string',
                'llave_hidrante' => 'required|string',
                'presion_agua' => 'required|string',
                'llave_fosa' => 'required|string',
                'ubicacion_fosa' => 'required|string',
                'hidrante_conectado_tubo' => 'required|string',
                'estado_hidrante' => 'required|string',
                'marca' => 'nullable|string',
                'anio' => 'nullable|string',
                'observaciones' => 'nullable|string',
                'oficial' => 'required|string'
            ]);

            $validated['create_user_id'] = auth()->id();
            $validated['update_user_id'] = auth()->id();

            // --- UBICACIÓN CORREGIDA ---
            // CALLE
            if ($request->id_calle && $request->id_calle !== '') {
                // Usar Select2
                $validated['id_calle'] = $request->id_calle;
                $validated['calle'] = CatalogoCalle::find($validated['id_calle'])?->Nomvial ?? null;
            } elseif ($request->calle && trim($request->calle) !== '') {
                // Usar input manual
                $validated['id_calle'] = 0;
                $validated['calle'] = trim($request->calle);
            } else {
                // Vacío (no debería llegar aquí por validación)
                $validated['id_calle'] = null;
                $validated['calle'] = null;
            }

            // Y_CALLE
            if ($request->id_y_calle && $request->id_y_calle !== '') {
                // Usar Select2
                $validated['id_y_calle'] = $request->id_y_calle;
                $validated['y_calle'] = CatalogoCalle::find($validated['id_y_calle'])?->Nomvial ?? null;
            } elseif ($request->y_calle && trim($request->y_calle) !== '') {
                // Usar input manual
                $validated['id_y_calle'] = 0;
                $validated['y_calle'] = trim($request->y_calle);
            } else {
                // Vacío
                $validated['id_y_calle'] = null;
                $validated['y_calle'] = null;
            }

            // COLONIA
            if ($request->id_colonia && $request->id_colonia !== '') {
                // Usar Select2
                $validated['id_colonia'] = $request->id_colonia;
                $validated['colonia'] = Colonias::find($validated['id_colonia'])?->NOMBRE ?? null;
            } elseif ($request->colonia && trim($request->colonia) !== '') {
                // Usar input manual
                $validated['id_colonia'] = 0;
                $validated['colonia'] = trim($request->colonia);
            } else {
                // Vacío
                $validated['id_colonia'] = null;
                $validated['colonia'] = null;
            }

            $validated['stat'] = Hidrante::calcularStat($validated);
            Hidrante::create($validated);
            
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hidrante creado exitosamente'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating hidrante:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Hidrante $hidrante)
    {
        try {
            \DB::beginTransaction();

            $validated = $request->validate([
                'fecha_inspeccion' => 'required|date',
                'numero_estacion' => 'required|string',
                'id_calle' => 'nullable|integer',
                'id_y_calle' => 'nullable|integer',
                'id_colonia' => 'nullable|integer',
                'calle' => 'nullable|string',
                'y_calle' => 'nullable|string',
                'colonia' => 'nullable|string',
                'llave_hidrante' => 'required|string',
                'presion_agua' => 'required|string',
                'llave_fosa' => 'required|string',
                'ubicacion_fosa' => 'required|string',
                'hidrante_conectado_tubo' => 'required|string',
                'estado_hidrante' => 'required|string',
                'marca' => 'required|string',
                'anio' => 'required|string',
                'observaciones' => 'nullable|string',
                'oficial' => 'required|string'
            ]);

            $validated['update_user_id'] = auth()->id();

            // --- UBICACIÓN CORREGIDA ---
            // CALLE
            if ($request->id_calle && $request->id_calle !== '') {
                // Usar Select2
                $validated['id_calle'] = $request->id_calle;
                $validated['calle'] = CatalogoCalle::find($validated['id_calle'])?->Nomvial ?? null;
            } elseif ($request->calle && trim($request->calle) !== '') {
                // Usar input manual
                $validated['id_calle'] = 0;
                $validated['calle'] = trim($request->calle);
            } else {
                // Vacío (no debería llegar aquí por validación)
                $validated['id_calle'] = null;
                $validated['calle'] = null;
            }

            // Y_CALLE
            if ($request->id_y_calle && $request->id_y_calle !== '') {
                // Usar Select2
                $validated['id_y_calle'] = $request->id_y_calle;
                $validated['y_calle'] = CatalogoCalle::find($validated['id_y_calle'])?->Nomvial ?? null;
            } elseif ($request->y_calle && trim($request->y_calle) !== '') {
                // Usar input manual
                $validated['id_y_calle'] = 0;
                $validated['y_calle'] = trim($request->y_calle);
            } else {
                // Vacío
                $validated['id_y_calle'] = null;
                $validated['y_calle'] = null;
            }

            // COLONIA
            if ($request->id_colonia && $request->id_colonia !== '') {
                // Usar Select2
                $validated['id_colonia'] = $request->id_colonia;
                $validated['colonia'] = Colonias::find($validated['id_colonia'])?->NOMBRE ?? null;
            } elseif ($request->colonia && trim($request->colonia) !== '') {
                // Usar input manual
                $validated['id_colonia'] = 0;
                $validated['colonia'] = trim($request->colonia);
            } else {
                // Vacío
                $validated['id_colonia'] = null;
                $validated['colonia'] = null;
            }

            $validated['stat'] = Hidrante::calcularStat($validated);
            $hidrante->update($validated);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hidrante actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating hidrante:', [
                'id' => $hidrante->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request, Hidrante $hidrante)
    {
        try {
            // Si solo necesitamos la información del tipo
            if ($request->has('_only_tipo')) {
                // Si se proporciona id_calle, obtenemos Tipovial
                if ($request->has('id_calle')) {
                    $calle = CatalogoCalle::find($request->id_calle);
                    return response()->json(['tipovial' => $calle ? $calle->Tipovial : null]);
                }
                // Si se proporciona id_y_calle
                elseif ($request->has('id_y_calle')) {
                    $calle = CatalogoCalle::find($request->id_y_calle);
                    return response()->json(['tipovial' => $calle ? $calle->Tipovial : null]);
                }
                // Si se proporciona id_colonia
                elseif ($request->has('id_colonia')) {
                    $colonia = Colonias::find($request->id_colonia);
                    return response()->json(['tipo' => $colonia ? $colonia->TIPO : null]);
                }
                
                return response()->json(['error' => 'ID no proporcionado']);
            }

            // Comportamiento normal del método
            $calles = CatalogoCalle::select('IDKEY', 'Nomvial', 'Tipovial')
                ->orderBy('Nomvial')
                ->get();
                
            $colonias = Colonias::select('IDKEY', 'NOMBRE', 'TIPO')
                ->orderBy('NOMBRE')
                ->get();
            
            return view('partials.hidrante-form', compact('hidrante', 'calles', 'colonias'));
        } catch (\Exception $e) {
            \Log::error('Error loading hidrante form:', [
                'id' => $hidrante->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error al cargar los datos del hidrante'
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            // Si solo necesitamos la información del tipo
            if ($request->has('_only_tipo')) {
                // Si se proporciona id_calle, obtenemos Tipovial
                if ($request->has('id_calle')) {
                    $calle = CatalogoCalle::find($request->id_calle);
                    return response()->json(['tipovial' => $calle ? $calle->Tipovial : null]);
                }
                // Si se proporciona id_y_calle
                elseif ($request->has('id_y_calle')) {
                    $calle = CatalogoCalle::find($request->id_y_calle);
                    return response()->json(['tipovial' => $calle ? $calle->Tipovial : null]);
                }
                // Si se proporciona id_colonia
                elseif ($request->has('id_colonia')) {
                    $colonia = Colonias::find($request->id_colonia);
                    return response()->json(['tipo' => $colonia ? $colonia->TIPO : null]);
                }
                
                return response()->json(['error' => 'ID no proporcionado']);
            }
            
            // Comportamiento normal del método
            $calles = CatalogoCalle::select('IDKEY', 'Nomvial', 'Tipovial')
                ->orderBy('Nomvial')
                ->get();
                
            $colonias = Colonias::select('IDKEY', 'NOMBRE', 'TIPO')
                ->orderBy('NOMBRE')
                ->get();
        
            return view('partials.hidrante-create', compact('calles', 'colonias'));
        } catch (\Exception $e) {
            \Log::error('Error loading create form:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error al cargar el formulario de creación'
            ], 500);
        }
    }

    public function guardarConfiguracion(Request $request)
    {
        try {
            $validated = $request->validate([
                'configuracion' => 'required|array',
                'filtros_act' => 'nullable|array'
            ]);

            $filtros_act = $request->filtros_act ?? [];

            $config = ConfiguracionCapturista::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'configuracion' => $validated['configuracion'],
                    'filtros_act' => $filtros_act
                ]
            );

            return response()->json([
                'success' => true,
                'configuracion' => $config->configuracion,
                'filtros_act' => $config->filtros_act
            ]);

        } catch (\Exception $e) {
            \Log::error('Error guardando configuración:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al guardar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getConfiguracion()
    {
        try {
            $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();

            return response()->json([
                'configuracion' => $configuracion ? $configuracion->configuracion : ConfiguracionCapturista::getDefaultConfig(),
                'filtros_act' => $configuracion ? $configuracion->filtros_act : ConfiguracionCapturista::getDefaultFilters()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateFiltros(Request $request)
    {
        try {
            $validated = $request->validate([
                'filtros_act' => 'required|array'
            ]);

            // Asegurarse de que no hay campos duplicados
            $filtrosUnicos = [];
            $camposVistos = [];

            foreach ($validated['filtros_act'] as $filtro) {
                $partes = explode(':', $filtro);
                $campo = $partes[0];
                
                // Si es estado_hidrante, permitimos repeticiones
                if ($campo === 'estado_hidrante' || !in_array($campo, $camposVistos)) {
                    $filtrosUnicos[] = $filtro;
                    $camposVistos[] = $campo;
                }
            }

            $config = ConfiguracionCapturista::updateOrCreate(
                ['user_id' => auth()->id()],
                ['filtros_act' => $filtrosUnicos]
            );

            return response()->json([
                'success' => true,
                'filtros_act' => $config->filtros_act
            ]);

        } catch (\Exception $e) {
            \Log::error('Error actualizando filtros:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al actualizar filtros: ' . $e->getMessage()
            ], 500);
        }
    }

    // Agregar este método para obtener valores únicos para los filtros
    private function getDistinctValues($campo)
    {
        $values = Cache::remember("distinct_values_{$campo}", 60*30, function() use ($campo) {
            // Para fecha_inspeccion: mostrar solo año-mes (YYYY-MM)
            if ($campo === 'fecha_inspeccion') {
                // Usar GROUP BY para asegurar que solo tenemos entradas únicas por año-mes
                $values = \DB::table('hidrantes')
                    ->selectRaw("DATE_FORMAT(fecha_inspeccion, '%Y-%m') as fecha_mes")
                    ->whereNotNull('fecha_inspeccion')
                    ->groupBy('fecha_mes')
                    ->orderBy('fecha_mes', 'desc')
                    ->pluck('fecha_mes')
                    ->toArray();
                
                return $values;
            }
            // Para campos numéricos que requieren ordenamiento numérico
            else if (in_array($campo, ['numero_estacion'])) {
                return Hidrante::whereNotNull($campo)
                    ->where($campo, '!=', '')
                    ->distinct($campo)
                    ->orderByRaw("CAST({$campo} AS UNSIGNED)")
                    ->pluck($campo)
                    ->toArray();
            }
            // Para el resto de campos (ordenamiento alfabético)
            else {
                return Hidrante::select($campo)
                    ->whereNotNull($campo)
                    ->where($campo, '!=', '')
                    ->distinct()
                    ->orderBy($campo)
                    ->pluck($campo)
                    ->toArray();
            }
        });
        
        // Agregar opción "Con campo pendiente" para campos de ubicación
        if (in_array($campo, ['calle', 'y_calle', 'colonia'])) {
            array_unshift($values, 'Con campo pendiente');
        }
        
        return $values;
    }

    public function cargarPanelAuxiliar(Request $request)
    {
        // Asegurar que Carbon use español
        \Carbon\Carbon::setLocale('es');
        
        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();
        $filtros_act = $configuracion ? $configuracion->filtros_act : [];
        
        // Convertir filtros del formato antiguo al nuevo si es necesario
        $filtros_act = array_map(function($filtro) {
            if (is_string($filtro) && strpos($filtro, ':') !== false) {
                return explode(':', $filtro)[0];
            }
            return $filtro;
        }, $filtros_act);
        
        $modo = $request->input('modo', 'tabla');
        
        // Nombres para los encabezados
        $headerNames = [
            'fecha_inspeccion' => 'Fecha Inspección',
            'numero_estacion' => 'N° Estación',
            'calle' => 'Calle',
            'y_calle' => 'Y Calle',
            'colonia' => 'Colonia',
            'llave_fosa' => 'Llave Fosa',
            'llave_hidrante' => 'Llave Hidrante',
            'presion_agua' => 'Presión Agua',
            'estado_hidrante' => 'Estado',
            'marca' => 'Marca',
            'anio' => 'Año',
            'oficial' => 'Oficial'
        ];
        
        // Variables para el modo resumen
        $tipo_resumen = (int)$request->input('tipo', 0); // Obtener el tipo del parámetro de solicitud
        $porcentajes = [];
        $columnas = [];
        
        if ($modo === 'resumen') {
            switch($tipo_resumen) {
                case 1: // Presión
                    $categorias = ['ALTA', 'REGULAR', 'BAJA', 'NULA', 'SIN INFORMACION'];
                    $columnas = [
                        'ALTA' => ['clase' => 'bg-success text-white'],
                        'REGULAR' => ['clase' => 'bg-info text-white'],
                        'BAJA' => ['clase' => 'bg-warning text-dark'],
                        'NULA' => ['clase' => 'bg-danger text-white'],
                        'SIN INFORMACION' => ['clase' => 'bg-secondary text-white']
                    ];
                    
                    // Calcular porcentajes para presión
                    $totales = Hidrante::count();
                    
                    if ($totales > 0) {
                        // Contar específicamente cada categoría
                        $alta = Hidrante::where('presion_agua', 'ALTA')->count();
                        $regular = Hidrante::where('presion_agua', 'REGULAR')->count();
                        $baja = Hidrante::where('presion_agua', 'BAJA')->count();
                        $nula = Hidrante::where('presion_agua', 'NULA')->count();
                        
                        // Para "SIN INFORMACION" incluir nulos, vacíos, 'S/I' y 'SIN INFORMACION'
                        $sinInfo = Hidrante::where('presion_agua', 'SIN INFORMACION')
                            ->orWhere('presion_agua', 'S/I')
                            ->orWhere('presion_agua', '')
                            ->orWhereNull('presion_agua')
                            ->count();
                        
                        $porcentajes = [
                            'ALTA' => round(($alta / $totales) * 100),
                            'REGULAR' => round(($regular / $totales) * 100),
                            'BAJA' => round(($baja / $totales) * 100),
                            'NULA' => round(($nula / $totales) * 100),
                            'SIN INFORMACION' => round(($sinInfo / $totales) * 100)
                        ];
                    }
                    break;
                    
                case 2: // Llave de hidrante
                    $categorias = ['CUADRO', 'PENTAGONO', 'SIN INFORMACION'];
                    $columnas = [
                        'CUADRO' => ['clase' => 'bg-primary text-white'],
                        'PENTAGONO' => ['clase' => 'bg-info text-white'],
                        'SIN INFORMACION' => ['clase' => 'bg-secondary text-white']
                    ];
                    
                    // Calcular porcentajes para llave de hidrante
                    $totales = Hidrante::count();
                    
                    if ($totales > 0) {
                        $cuadro = Hidrante::where('llave_hidrante', 'CUADRO')->count();
                        $pentagono = Hidrante::where('llave_hidrante', 'PENTAGONO')->count();
                        
                        // Para "SIN INFORMACION" incluir nulos, vacíos, 'S/I' y 'SIN INFORMACION'
                        $sinInfo = Hidrante::where('llave_hidrante', 'SIN INFORMACION')
                            ->orWhere('llave_hidrante', 'S/I')
                            ->orWhere('llave_hidrante', '')
                            ->orWhereNull('llave_hidrante')
                            ->count();
                        
                        $porcentajes = [
                            'CUADRO' => round(($cuadro / $totales) * 100),
                            'PENTAGONO' => round(($pentagono / $totales) * 100),
                            'SIN INFORMACION' => round(($sinInfo / $totales) * 100)
                        ];
                    }
                    break;
                    
                case 3: // Llave de fosa
                    $categorias = ['CUADRO', 'VOLANTE', 'SIN INFORMACION'];
                    $columnas = [
                        'CUADRO' => ['clase' => 'bg-primary text-white'],
                        'VOLANTE' => ['clase' => 'bg-info text-white'],
                        'SIN INFORMACION' => ['clase' => 'bg-secondary text-white']
                    ];
                    
                    // Calcular porcentajes para llave de fosa
                    $totales = Hidrante::count();
                    
                    if ($totales > 0) {
                        $cuadro = Hidrante::where('llave_fosa', 'CUADRO')->count();
                        $volante = Hidrante::where('llave_fosa', 'VOLANTE')->count();
                        
                        // Para "SIN INFORMACION" incluir nulos, vacíos, 'S/I' y 'SIN INFORMACION'
                        $sinInfo = Hidrante::where('llave_fosa', 'SIN INFORMACION')
                            ->orWhere('llave_fosa', 'S/I')
                            ->orWhere('llave_fosa', '')
                            ->orWhereNull('llave_fosa')
                            ->count();
                        
                        $porcentajes = [
                            'CUADRO' => round(($cuadro / $totales) * 100),
                            'VOLANTE' => round(($volante / $totales) * 100),
                            'SIN INFORMACION' => round(($sinInfo / $totales) * 100)
                        ];
                    }
                    break;
                    
                default: // Estado (tipo 0)
                    $categorias = ['EN SERVICIO', 'FUERA DE SERVICIO', 'SOLO BASE'];
                    $columnas = [
                        'EN SERVICIO' => ['clase' => 'bg-info text-white'],
                        'FUERA DE SERVICIO' => ['clase' => 'bg-danger text-white'],
                        'SOLO BASE' => ['clase' => 'bg-warning text-dark']
                    ];
                    
                    // Calcular porcentajes para el estado por defecto
                    $totales = Hidrante::count();
                    
                    if ($totales > 0) {
                        $enServicio = Hidrante::where('estado_hidrante', 'EN SERVICIO')->count();
                        $fueraServicio = Hidrante::where('estado_hidrante', 'FUERA DE SERVICIO')->count();
                        $soloBase = Hidrante::where('estado_hidrante', 'SOLO BASE')->count();
                        
                        $porcentajes = [
                            'EN SERVICIO' => round(($enServicio / $totales) * 100),
                            'FUERA DE SERVICIO' => round(($fueraServicio / $totales) * 100),
                            'SOLO BASE' => round(($soloBase / $totales) * 100)
                        ];
                    }
            }
        }
        
        // Obtener opciones para los selectores de filtro
        $opciones_filtro = [];
        foreach ($filtros_act as $campo) {
            // Eliminar cualquier parte de valor si existe
            $campo = is_string($campo) && strpos($campo, ':') !== false ? 
                explode(':', $campo)[0] : $campo;
                
            if (!isset($opciones_filtro[$campo])) {
                $opciones_filtro[$campo] = $this->getDistinctValues($campo);
            }
        }
        
        return view('partials.configuracion-param-auxiliar', 
            compact('filtros_act', 'modo', 'headerNames', 'opciones_filtro', 'tipo_resumen', 'porcentajes', 'columnas'))
            ->render();
    }

    public function dataTable(Request $request)
    {
        try {
            $query = Hidrante::with([
                'coloniaLocacion',
                'callePrincipal',
                'calleSecundaria'
            ]);

            // Procesar filtros adicionales para columnas no visibles
            if ($request->has('filtros_adicionales')) {
                $filtrosAdicionales = json_decode($request->filtros_adicionales, true);
                
                if (is_array($filtrosAdicionales)) {
                    \Log::info('Filtros adicionales recibidos:', $filtrosAdicionales);
                    
                    foreach ($filtrosAdicionales as $campo => $valor) {
                        // Manejar filtros de ubicación "Con campo pendiente"
                        if (in_array($campo, ['calle', 'y_calle', 'colonia']) && $valor === 'Con campo pendiente') {
                            $idCampo = 'id_' . $campo;
                            $query->where($idCampo, 0);
                            continue;
                        }
                        
                        // Manejar filtro de fecha por año-mes
                        if ($campo === 'fecha_inspeccion' && $valor) {
                            $query->whereRaw("DATE_FORMAT(fecha_inspeccion, '%Y-%m') = ?", [$valor]);
                            continue;
                        }
                        
                        // Manejo estándar para otros campos
                        if (\Schema::hasColumn('hidrantes', $campo)) {
                            if ($valor === '') {
                                $query->where(function($q) use ($campo) {
                                    $q->whereNull($campo)->orWhere($campo, '');
                                });
                            } else {
                                $query->where($campo, $valor);
                            }
                        }
                    }
                }
            }
            
            return DataTables::eloquent($query)
                ->addColumn('acciones', function($hidrante) {
                    $botones = '
                        <button class="btn btn-sm btn-primary view-hidrante" title="Ver el reporte del hidrante" data-hidrante-id="'.$hidrante->id.'">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning edit-hidrante" title="Editar la información del hidrante" data-hidrante-id="'.$hidrante->id.'">
                            <i class="bi bi-pen"></i>
                        </button>
                    ';

                    if ($hidrante->stat === '000') {
                        $botones .= '
                            <button class="btn btn-sm btn-success activar-hidrante" title="Activar hidrante" data-hidrante-id="'.$hidrante->id.'">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        ';
                    } else {
                        $botones .= '
                            <button class="btn btn-sm btn-danger desactivar-hidrante" title="Desactivar hidrante" data-hidrante-id="'.$hidrante->id.'">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        ';
                    }
                    return $botones;
                })
                ->filterColumn('numero_estacion', function($query, $keyword) {
                    // Si es búsqueda vacía
                    if ($keyword === '') {
                        $query->where(function($q) {
                            $q->whereNull('numero_estacion')->orWhere('numero_estacion', '');
                        });
                    } else {
                        $query->where('numero_estacion', $keyword);
                    }
                })
                ->filterColumn('estado_hidrante', function($query, $keyword) {
                    if ($keyword === '') {
                        $query->where(function($q) {
                            $q->whereNull('estado_hidrante')->orWhere('estado_hidrante', '');
                        });
                    } else {
                        $query->where('estado_hidrante', $keyword);
                    }
                })
                ->filterColumn('presion_agua', function($query, $keyword) {
                    if ($keyword === '') {
                        $query->where(function($q) {
                            $q->whereNull('presion_agua')->orWhere('presion_agua', '');
                        });
                    } else {
                        $query->where('presion_agua', $keyword);
                    }
                })
                ->filterColumn('colonia', function($query, $keyword) {
                    if ($keyword === '') {
                        $query->where(function($q) {
                            $q->whereNull('colonia')->orWhere('colonia', '');
                        });
                    } else {
                        $query->where('colonia', $keyword);
                    }
                })
                // Añade aquí más filterColumn para otros campos que necesiten filtrado exacto
                ->editColumn('numero_estacion', function($hidrante) {
                    if (is_null($hidrante->numero_estacion) || $hidrante->numero_estacion === '') {
                        return 'N/A';
                    }
                    return $hidrante->numero_estacion;
                })
                ->editColumn('calle', function($hidrante) {
                    if (is_null($hidrante->id_calle)) {
                        return 'N/A';
                    }
                    if ($hidrante->id_calle == 0) {
                        return $hidrante->calle ? $hidrante->calle . '*' : 'Pendiente';
                    }
                    if ($hidrante->callePrincipal) {
                        return '<span title="' . $hidrante->callePrincipal->Tipovial . '">' 
                            . $hidrante->callePrincipal->Nomvial . '</span>';
                    }
                    return 'N/A';
                })
                ->editColumn('y_calle', function($hidrante) {
                    if (is_null($hidrante->id_y_calle)) {
                        return 'N/A';
                    }
                    if ($hidrante->id_y_calle == 0) {
                        return $hidrante->y_calle ? $hidrante->y_calle . '*' : 'Pendiente';
                    }
                    if ($hidrante->calleSecundaria) {
                        return '<span title="' . $hidrante->calleSecundaria->Tipovial . '">' 
                            . $hidrante->calleSecundaria->Nomvial . '</span>';
                    }
                    return 'N/A';
                })
                ->editColumn('colonia', function($hidrante) {
                    if (is_null($hidrante->id_colonia)) {
                        return 'N/A';
                    }
                    if ($hidrante->id_colonia == 0) {
                        return $hidrante->colonia ? $hidrante->colonia . '*' : 'Pendiente';
                    }
                    if ($hidrante->coloniaLocacion) {
                        return '<span title="' . $hidrante->coloniaLocacion->TIPO . '">' 
                            . $hidrante->coloniaLocacion->NOMBRE . '</span>';
                    }
                    return 'N/A';
                })
                ->editColumn('fecha_inspeccion', function($hidrante) {
                    return $hidrante->fecha_inspeccion ? $hidrante->fecha_inspeccion->format('Y-m-d') : 'N/A';
                })
                ->rawColumns(['acciones', 'calle', 'y_calle', 'colonia'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Error en dataTable:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error al procesar la solicitud'
            ], 500);
        }
    }

    public function configuracionModal()
    {
        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();
        $columnas = $configuracion ? $configuracion->configuracion : ConfiguracionCapturista::getDefaultConfig();
        $filtros_act = $configuracion ? $configuracion->filtros_act : ConfiguracionCapturista::getDefaultFilters();
        
        // Convertir filtros del formato antiguo al nuevo si es necesario
        $filtros_act = array_map(function($filtro) {
            if (is_string($filtro) && strpos($filtro, ':') !== false) {
                return explode(':', $filtro)[0];
            }
            return $filtro;
        }, $filtros_act);

        return view('partials.configuracion-param-modal', compact('columnas', 'filtros_act'))->render();
    }

    public function view(Hidrante $hidrante)
    {
        $hidrante->load(['coloniaLocacion', 'callePrincipal', 'calleSecundaria']);
        return view('partials.hidrante-view', compact('hidrante'))->render();
    }

    public function desactivar(Hidrante $hidrante)
    {
        $hidrante->update(['stat' => '000']);
        return response()->json(['success' => true]);
    }

    public function activar(Hidrante $hidrante)
    {
        // Recalcula el stat con los datos actuales del hidrante
        $stat = Hidrante::calcularStat($hidrante->toArray());
        $hidrante->update(['stat' => $stat]);
        return response()->json(['success' => true]);
    }

    public function resumenHidrantes(Request $request)
    {
        $tipo = $request->query('tipo', 'estado');
        
        switch($tipo) {
            case 'presion':
                return $this->resumenPresion();
            case 'hidrante':
                return $this->resumenLlavesHidrante();
            case 'fosa':
                return $this->resumenLlavesFosa();
            default:
                return $this->resumenEstadoHidrante();
        }
    }

    // Método original renombrado
    private function resumenEstadoHidrante()
    {
        // Obtén todos los hidrantes
        $hidrantes = Hidrante::all();

        // Agrupa por estación y estado
        $estaciones = [];
        $estados = ['EN SERVICIO', 'FUERA DE SERVICIO', 'SOLO BASE'];
        $totales = [
            'EN SERVICIO' => 0,
            'FUERA DE SERVICIO' => 0,
            'SOLO BASE' => 0,
            'TOTAL' => 0
        ];

        foreach ($hidrantes as $h) {
            $est = $h->numero_estacion ?: 'N/A';
            if (!isset($estaciones[$est])) {
                $estaciones[$est] = [
                    'EN SERVICIO' => 0,
                    'FUERA DE SERVICIO' => 0,
                    'SOLO BASE' => 0,
                    'TOTAL' => 0
                ];
            }
            $estado = strtoupper(trim($h->estado_hidrante));
            if (!in_array($estado, $estados)) $estado = 'EN SERVICIO'; // Default/fallback
            $estaciones[$est][$estado]++;
            $estaciones[$est]['TOTAL']++;
            $totales[$estado]++;
            $totales['TOTAL']++;
        }

        // Totales F.S. + S.B. por estación
        foreach ($estaciones as $est => &$row) {
            $row['FS_SB'] = $row['FUERA DE SERVICIO'] + $row['SOLO BASE'];
        }

        // Ordenar estaciones numéricamente
        uksort($estaciones, function($a, $b) {
            if ($a === 'N/A') return 1;  // N/A siempre al final
            if ($b === 'N/A') return -1;
            return (int)$a - (int)$b;    // Ordenar numéricamente
        });

        // Porcentajes
        $porcentajes = [
            'EN SERVICIO' => $totales['TOTAL'] ? round($totales['EN SERVICIO'] / $totales['TOTAL'] * 100) : 0,
            'FUERA DE SERVICIO' => $totales['TOTAL'] ? round($totales['FUERA DE SERVICIO'] / $totales['TOTAL'] * 100) : 0,
            'SOLO BASE' => $totales['TOTAL'] ? round($totales['SOLO BASE'] / $totales['TOTAL'] * 100) : 0,
        ];

        return view('partials.hidrantes-resumen', [
            'estaciones' => $estaciones, 
            'totales' => $totales, 
            'porcentajes' => $porcentajes,
            'tipo_resumen' => 0,
            'titulo_resumen' => 'Estado',
            'columnas' => [
                'FUNCIONANDO' => ['clase' => 'bg-info text-white', 'key' => 'EN SERVICIO'],
                'FUERA DE SERVICIO' => ['clase' => 'bg-danger text-white', 'key' => 'FUERA DE SERVICIO'],
                'SOLO BASE' => ['clase' => 'bg-warning text-dark', 'key' => 'SOLO BASE']
            ],
            'ultima_columna' => [
                'titulo' => 'TOTAL F.S. + S.B.',
                'clase' => 'bg-warning text-dark',
                'key' => 'FS_SB'
            ]
        ]);
    }

    // Nuevo método para el resumen de presión
    private function resumenPresion()
    {
        // Obtener los hidrantes agrupados por estación y presión
        $estaciones = [];
        $categorias = ['ALTA', 'REGULAR', 'BAJA', 'NULA', 'SIN INFORMACION'];
        $totales = array_fill_keys($categorias, 0);
        $totales['TOTAL'] = 0;
        
        $hidrantes = Hidrante::selectRaw('numero_estacion, presion_agua, COUNT(*) as total')
            ->groupBy('numero_estacion', 'presion_agua')
            ->get();
        
        foreach ($hidrantes as $h) {
            $est = $h->numero_estacion ?: 'N/A';
            $presion = strtoupper(trim($h->presion_agua));
            
            if (!in_array($presion, $categorias)) {
                $presion = 'SIN INFORMACION';
            }
            
            if (!isset($estaciones[$est])) {
                $estaciones[$est] = array_fill_keys($categorias, 0);
                $estaciones[$est]['TOTAL'] = 0;
            }
            
            $estaciones[$est][$presion] += $h->total;
            $estaciones[$est]['TOTAL'] += $h->total;
            $totales[$presion] += $h->total;
            $totales['TOTAL'] += $h->total;
        }
        
        // Cálculo de totales de Alta+Regular por estación
        foreach ($estaciones as $est => &$row) {
            $row['ALTA_REGULAR'] = $row['ALTA'] + $row['REGULAR'];
        }
        
        // Porcentajes
        $porcentajes = [];
        foreach ($categorias as $cat) {
            $porcentajes[$cat] = $totales['TOTAL'] ? round($totales[$cat] / $totales['TOTAL'] * 100) : 0;
        }
        
        return view('partials.hidrantes-resumen', [
            'estaciones' => $estaciones, 
            'totales' => $totales, 
            'porcentajes' => $porcentajes,
            'tipo_resumen' => 1,
            'titulo_resumen' => 'Presión',
            'columnas' => [
                'ALTA' => ['clase' => 'bg-success text-white', 'key' => 'ALTA'],
                'REGULAR' => ['clase' => 'bg-info text-white', 'key' => 'REGULAR'],
                'BAJA' => ['clase' => 'bg-warning text-dark', 'key' => 'BAJA'],
                'NULA' => ['clase' => 'bg-danger text-white', 'key' => 'NULA'],
                'SIN INFORMACION' => ['clase' => 'bg-secondary text-white', 'key' => 'SIN INFORMACION']
            ],
            'ultima_columna' => [
                'titulo' => 'TOTAL ALTA + REGULAR',
                'clase' => 'bg-success text-white',
                'key' => 'ALTA_REGULAR'
            ]
        ]);
    }

    // Nuevo método para el resumen de llaves de hidrante
    private function resumenLlavesHidrante()
    {
        // Obtener los hidrantes agrupados por estación y tipo de llave
        $estaciones = [];
        $categorias = ['CUADRO', 'PENTAGONO', 'SIN INFORMACION'];
        $totales = array_fill_keys($categorias, 0);
        $totales['TOTAL'] = 0;
        
        $hidrantes = Hidrante::selectRaw('numero_estacion, llave_hidrante, COUNT(*) as total')
            ->groupBy('numero_estacion', 'llave_hidrante')
            ->get();
        
        foreach ($hidrantes as $h) {
            $est = $h->numero_estacion ?: 'N/A';
            $llave = strtoupper(trim($h->llave_hidrante));
            
            if (!in_array($llave, $categorias)) {
                $llave = 'SIN INFORMACION';
            }
            
            if (!isset($estaciones[$est])) {
                $estaciones[$est] = array_fill_keys($categorias, 0);
                $estaciones[$est]['TOTAL'] = 0;
            }
            
            $estaciones[$est][$llave] += $h->total;
            $estaciones[$est]['TOTAL'] += $h->total;
            $totales[$llave] += $h->total;
            $totales['TOTAL'] += $h->total;
        }
        
        // Cálculo de totales de Cuadro+Pentágono por estación
        foreach ($estaciones as $est => &$row) {
            $row['CUADRO_PENTAGONO'] = $row['CUADRO'] + $row['PENTAGONO'];
        }
        
        // Porcentajes
        $porcentajes = [];
        foreach ($categorias as $cat) {
            $porcentajes[$cat] = $totales['TOTAL'] ? round($totales[$cat] / $totales['TOTAL'] * 100) : 0;
        }
        
        return view('partials.hidrantes-resumen', [
            'estaciones' => $estaciones, 
            'totales' => $totales, 
            'porcentajes' => $porcentajes,
            'tipo_resumen' => 2,
            'titulo_resumen' => 'Llaves de Hidrante',
            'columnas' => [
                'CUADRO' => ['clase' => 'bg-primary text-white', 'key' => 'CUADRO'],
                'PENTAGONO' => ['clase' => 'bg-info text-white', 'key' => 'PENTAGONO'],
                'SIN INFORMACION' => ['clase' => 'bg-secondary text-white', 'key' => 'SIN INFORMACION']
            ],
            'ultima_columna' => [
                'titulo' => 'TOTAL CUADRO + PENTAGONO',
                'clase' => 'bg-success text-white',
                'key' => 'CUADRO_PENTAGONO'
            ]
        ]);
    }

    // Nuevo método para el resumen de llaves de fosa
    private function resumenLlavesFosa()
    {
        // Obtener los hidrantes agrupados por estación y tipo de llave de fosa
        $estaciones = [];
        $categorias = ['CUADRO', 'VOLANTE', 'SIN INFORMACION'];
        $totales = array_fill_keys($categorias, 0);
        $totales['TOTAL'] = 0;
        
        $hidrantes = Hidrante::selectRaw('numero_estacion, llave_fosa, COUNT(*) as total')
            ->groupBy('numero_estacion', 'llave_fosa')
            ->get();
        
        foreach ($hidrantes as $h) {
            $est = $h->numero_estacion ?: 'N/A';
            $llave = strtoupper(trim($h->llave_fosa));
            
            if (!in_array($llave, $categorias)) {
                $llave = 'SIN INFORMACION';
            }
            
            if (!isset($estaciones[$est])) {
                $estaciones[$est] = array_fill_keys($categorias, 0);
                $estaciones[$est]['TOTAL'] = 0;
            }
            
            $estaciones[$est][$llave] += $h->total;
            $estaciones[$est]['TOTAL'] += $h->total;
            $totales[$llave] += $h->total;
            $totales['TOTAL'] += $h->total;
        }
        
        // Cálculo de totales de Cuadro+Volante por estación
        foreach ($estaciones as $est => &$row) {
            $row['CUADRO_VOLANTE'] = $row['CUADRO'] + $row['VOLANTE'];
        }
        
        // Porcentajes
        $porcentajes = [];
        foreach ($categorias as $cat) {
            $porcentajes[$cat] = $totales['TOTAL'] ? round($totales[$cat] / $totales['TOTAL'] * 100) : 0;
        }
        
        return view('partials.hidrantes-resumen', [
            'estaciones' => $estaciones, 
            'totales' => $totales, 
            'porcentajes' => $porcentajes,
            'tipo_resumen' => 3,
            'titulo_resumen' => 'Llaves de Fosa',
            'columnas' => [
                'CUADRO' => ['clase' => 'bg-primary text-white', 'key' => 'CUADRO'],
                'VOLANTE' => ['clase' => 'bg-info text-white', 'key' => 'VOLANTE'],
                'SIN INFORMACION' => ['clase' => 'bg-secondary text-white', 'key' => 'SIN INFORMACION']
            ],
            'ultima_columna' => [
                'titulo' => 'TOTAL CUADRO + VOLANTE',
                'clase' => 'bg-success text-white',
                'key' => 'CUADRO_VOLANTE'
            ]
        ]);
    }
}