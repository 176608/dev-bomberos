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
                'id_calle' => 'required|integer',
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
                'anio' => 'nullable|integer',
                'observaciones' => 'nullable|string',
                'oficial' => 'required|string'
            ]);

            // IDs de usuario
            $validated['create_user_id'] = auth()->id();
            $validated['update_user_id'] = auth()->id();

            // --- UBICACIÓN ---
            // CALLE
            if ($request->id_calle == 0) {
                $validated['calle'] = 'Pendiente';
                $validated['id_calle'] = 0;
            } elseif ($request->id_calle) {
                $validated['id_calle'] = $request->id_calle;
                $validated['calle'] = CatalogoCalle::find($validated['id_calle'])?->Nomvial ?? null;
            } else {
                // Esto nunca debería pasar porque id_calle es required, pero por robustez:
                $validated['calle'] = null;
                $validated['id_calle'] = null;
            }

            // Y_CALLE
            if ($request->id_y_calle === 0 || $request->id_y_calle === '0') {
                $validated['y_calle'] = 'Pendiente';
                $validated['id_y_calle'] = 0;
            } elseif ($request->id_y_calle) {
                $validated['id_y_calle'] = $request->id_y_calle;
                $validated['y_calle'] = CatalogoCalle::find($validated['id_y_calle'])?->Nomvial ?? null;
            } else {
                $validated['y_calle'] = null;
                $validated['id_y_calle'] = null;
            }

            // COLONIA
            if ($request->id_colonia === 0 || $request->id_colonia === '0') {
                $validated['colonia'] = 'Pendiente';
                $validated['id_colonia'] = 0;
            } elseif ($request->id_colonia) {
                $validated['id_colonia'] = $request->id_colonia;
                $validated['colonia'] = Colonias::find($validated['id_colonia'])?->NOMBRE ?? null;
            } else {
                $validated['colonia'] = null;
                $validated['id_colonia'] = null;
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
                'data' => $validated ?? null
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
                'id_calle' => 'required|integer',
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
                'anio' => 'required|integer',
                'observaciones' => 'nullable|string',
                'oficial' => 'required|string'
            ]);

            $validated['update_user_id'] = auth()->id();

            // --- UBICACIÓN ---
            // CALLE
            if ($request->id_calle == 0) {
                $validated['calle'] = 'Pendiente';
                $validated['id_calle'] = 0;
            } elseif ($request->id_calle) {
                $validated['id_calle'] = $request->id_calle;
                $validated['calle'] = CatalogoCalle::find($validated['id_calle'])?->Nomvial ?? null;
            } else {
                // Esto nunca debería pasar porque id_calle es required, pero por robustez:
                $validated['calle'] = null;
                $validated['id_calle'] = null;
            }

            // Y_CALLE
            if ($request->id_y_calle === 0 || $request->id_y_calle === '0') {
                $validated['y_calle'] = 'Pendiente';
                $validated['id_y_calle'] = 0;
            } elseif ($request->id_y_calle) {
                $validated['id_y_calle'] = $request->id_y_calle;
                $validated['y_calle'] = CatalogoCalle::find($validated['id_y_calle'])?->Nomvial ?? null;
            } else {
                $validated['y_calle'] = null;
                $validated['id_y_calle'] = null;
            }

            // COLONIA
            if ($request->id_colonia === 0 || $request->id_colonia === '0') {
                $validated['colonia'] = 'Pendiente';
                $validated['id_colonia'] = 0;
            } elseif ($request->id_colonia) {
                $validated['id_colonia'] = $request->id_colonia;
                $validated['colonia'] = Colonias::find($validated['id_colonia'])?->NOMBRE ?? null;
            } else {
                $validated['colonia'] = null;
                $validated['id_colonia'] = null;
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

    public function edit(Hidrante $hidrante)
    {
        try {
            $calles = CatalogoCalle::select('IDKEY', 'Nomvial')
                ->orderBy('Nomvial')
                ->get();
                
            $colonias = Colonias::select('IDKEY', 'NOMBRE')
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

    public function create()
    {
        $calles = CatalogoCalle::select('IDKEY', 'Nomvial')
            ->orderBy('Nomvial')
            ->get();
            
        $colonias = Colonias::select('IDKEY', 'NOMBRE')
            ->orderBy('NOMBRE')
            ->get();
        
        return view('partials.hidrante-create', compact('calles', 'colonias'));
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
            // Para campos de tipo fecha que requieren formateo
            if ($campo === 'fecha_inspeccion') {
                return Hidrante::whereNotNull($campo)
                    ->select(\DB::raw("DATE_FORMAT(fecha_inspeccion, '%Y-%m-%d') as value"))
                    ->distinct()
                    ->orderBy('value')
                    ->pluck('value')
                    ->toArray();
            }
            // Para campos numéricos que requieren ordenamiento numérico
            else if (in_array($campo, ['numero_estacion', 'anio'])) {
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
        
        return $values;
    }

    public function cargarPanelAuxiliar(Request $request)
    {
        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();
        $filtros_act = $configuracion ? $configuracion->filtros_act : [];
        
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
        
        // Obtener opciones para los selectores de filtro
        $opciones_filtro = [];
        foreach ($filtros_act as $filtro) {
            $partes = explode(':', $filtro);
            $campo = $partes[0];
            
            if (!isset($opciones_filtro[$campo])) {
                $opciones_filtro[$campo] = $this->getDistinctValues($campo);
            }
        }
        
        return view('partials.configuracion-param-auxiliar', 
            compact('filtros_act', 'modo', 'headerNames', 'opciones_filtro'))
            ->render();
    }

    public function dataTable(Request $request)
    {
        $query = Hidrante::with([
            'coloniaLocacion',
            'callePrincipal',
            'calleSecundaria'
        ]);

        // Procesar filtros adicionales para columnas no visibles
        if ($request->has('filtros_adicionales')) {
            $filtrosAdicionales = json_decode($request->filtros_adicionales, true);
            
            if (is_array($filtrosAdicionales)) {
                foreach ($filtrosAdicionales as $campo => $valor) {
                    if (\Schema::hasColumn('hidrantes', $campo)) {
                        // Si el valor está vacío, buscamos registros vacíos o NULL
                        if ($valor === '') {
                            $query->where(function($q) use ($campo) {
                                $q->whereNull($campo)->orWhere($campo, '');
                            });
                        } else {
                            // Búsqueda exacta para valores no vacíos
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
    }

    public function configuracionModal()
    {
        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();
        $columnas = $configuracion ? $configuracion->configuracion : ConfiguracionCapturista::getDefaultConfig();
        $filtros_act = $configuracion ? $configuracion->filtros_act : ConfiguracionCapturista::getDefaultFilters();

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

    public function resumenHidrantes()
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

        // Porcentajes
        $porcentajes = [
            'EN SERVICIO' => $totales['TOTAL'] ? round($totales['EN SERVICIO'] / $totales['TOTAL'] * 100) : 0,
            'FUERA DE SERVICIO' => $totales['TOTAL'] ? round($totales['FUERA DE SERVICIO'] / $totales['TOTAL'] * 100) : 0,
            'SOLO BASE' => $totales['TOTAL'] ? round($totales['SOLO BASE'] / $totales['TOTAL'] * 100) : 0,
        ];

        return view('partials.hidrantes-resumen', compact('estaciones', 'totales', 'porcentajes'));
    }
}