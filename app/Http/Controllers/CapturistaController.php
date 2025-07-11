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
                'configuracion' => 'required|array'
            ]);

            $config = ConfiguracionCapturista::updateOrCreate(
                ['user_id' => auth()->id()],
                ['configuracion' => $validated['configuracion']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Configuración guardada exitosamente',
                'configuracion' => $config->configuracion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error guardando configuración backend:', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración backend: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getConfiguracion()
    {
        try {
            $config = ConfiguracionCapturista::where('user_id', auth()->id())->first();
            return response()->json([
                'success' => true,
                'configuracion' => $config ? $config->configuracion : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la configuración de backend'
            ], 500);
        }
    }

    public function dataTable(Request $request)
    {
        $query = Hidrante::with([
            'coloniaLocacion',
            'callePrincipal',
            'calleSecundaria'
        ]);

        return DataTables::eloquent($query)
            ->addColumn('acciones', function($hidrante) {
                $botones = '
                    <button class="btn btn-sm btn-primary view-hidrante" title="Ver la información del hidrante" data-hidrante-id="'.$hidrante->id.'">
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
            ->editColumn('numero_estacion', function($hidrante) {
                if (is_null($hidrante->numero_estacion) || $hidrante->numero_estacion === '') {
                    return 'N/A';
                }
                return $hidrante->numero_estacion;
            })
            ->editColumn('calle', function($hidrante) {
                if (is_null($hidrante->id_calle)) {
                    return 'Sin definir';
                }
                if ($hidrante->id_calle == 0) {
                    return 'Pendiente';
                }
                return $hidrante->callePrincipal?->Nomvial ?? 'Sin definir';
            })
            ->editColumn('y_calle', function($hidrante) {
                if (is_null($hidrante->id_y_calle)) {
                    return 'Sin definir';
                }
                if ($hidrante->id_y_calle == 0) {
                    return 'Pendiente';
                }
                return $hidrante->calleSecundaria?->Nomvial ?? 'Sin definir';
            })
            ->editColumn('colonia', function($hidrante) {
                if (is_null($hidrante->id_colonia)) {
                    return 'Sin definir';
                }
                if ($hidrante->id_colonia == 0) {
                    return 'Pendiente';
                }
                return $hidrante->coloniaLocacion?->NOMBRE ?? 'Sin definir';
            })
            ->editColumn('fecha_inspeccion', function($hidrante) {
                return $hidrante->fecha_inspeccion ? $hidrante->fecha_inspeccion->format('Y-m-d') : 'N/A';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function configuracionModal()
    {
        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();
        $columnas = $configuracion ? $configuracion->configuracion : ConfiguracionCapturista::getDefaultConfig();

        return view('partials.configuracion-param-modal', compact('columnas'))->render();
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