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

        /*// Obtener la configuración del usuario -- OLD
        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())
            ->first();
        // Obtener la configuración del usuario -- OLD
        $columnas = $configuracion ? $configuracion->configuracion : ConfiguracionCapturista::getDefaultConfig();
        */

        $configuracion = ConfiguracionCapturista::where('user_id', auth()->id())->first();
        $columnas = $configuracion ? $configuracion->configuracion : ConfiguracionCapturista::getDefaultConfig();

        $headerNames = [
            'fecha_inspeccion' => 'Fecha Inspección',
            'fecha_tentativa' => 'Fecha Tentativa', 
            'numero_estacion' => 'N° Estación',
            'calle' => 'Calle Principal',
            'y_calle' => 'Calle Secundaria',
            'colonia' => 'Colonia',
            'llave_hidrante' => 'Llave Hidrante',
            'presion_agua' => 'Presión Agua',
            'color' => 'Color',
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
                'fecha_tentativa' => 'required|date',
                'numero_estacion' => 'required|string',
                'id_calle' => 'nullable|integer',
                'id_y_calle' => 'nullable|integer',
                'id_colonia' => 'nullable|integer',
                'calle' => 'nullable|string',
                'y_calle' => 'nullable|string',
                'colonia' => 'nullable|string',
                'llave_hidrante' => 'required|string',
                'presion_agua' => 'required|string',
                'color' => 'required|string',
                'llave_fosa' => 'required|string',
                'ubicacion_fosa' => 'required|string',
                'hidrante_conectado_tubo' => 'required|string',
                'estado_hidrante' => 'required|string',
                'marca' => 'required|string',
                'anio' => 'required|integer',
                'observaciones' => 'nullable|string',
                'oficial' => 'required|string'
            ]);

            // IDs de usuario
            $validated['create_user_id'] = auth()->id();
            $validated['update_user_id'] = auth()->id();

            // --- UBICACIÓN ---
            $validated['id_calle'] = $request->id_calle;
            $validated['id_y_calle'] = ($request->id_y_calle === null || $request->id_y_calle === '' || $request->id_y_calle === 'null') ? null : $request->id_y_calle;
            $validated['id_colonia'] = ($request->id_colonia === null || $request->id_colonia === '' || $request->id_colonia === 'null') ? null : $request->id_colonia;

            $validated['calle'] = ($validated['id_calle'] == 0)
                ? 'Pendiente'
                : ($validated['id_calle']
                    ? (CatalogoCalle::find($validated['id_calle'])?->Nomvial ?? 'Sin definir')
                    : 'Sin definir');

            $validated['y_calle'] = ($validated['id_y_calle'] == 0)
                ? 'Pendiente'
                : ($validated['id_y_calle']
                    ? (CatalogoCalle::find($validated['id_y_calle'])?->Nomvial ?? 'Sin definir')
                    : 'Sin definir');

            $validated['colonia'] = ($validated['id_colonia'] == 0)
                ? 'Pendiente'
                : ($validated['id_colonia']
                    ? (Colonias::find($validated['id_colonia'])?->NOMBRE ?? 'Sin definir')
                    : 'Sin definir');

            // Remover valores nulos
            $validated = array_filter($validated, function($value) {
                return $value !== null;
            });

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
                'fecha_tentativa' => 'required|date',
                'numero_estacion' => 'required|string',
                'id_calle' => 'nullable|integer',
                'id_y_calle' => 'nullable|integer',
                'id_colonia' => 'nullable|integer',
                'calle' => 'nullable|string',
                'y_calle' => 'nullable|string',
                'colonia' => 'nullable|string',
                'llave_hidrante' => 'required|string',
                'presion_agua' => 'required|string',
                'color' => 'required|string',
                'llave_fosa' => 'required|string',
                'ubicacion_fosa' => 'required|string',
                'hidrante_conectado_tubo' => 'required|string',
                'estado_hidrante' => 'required|string',
                'marca' => 'required|string',
                'anio' => 'required|integer',
                'observaciones' => 'nullable|string',
                'oficial' => 'required|string'
            ]);

            // Actualizar update_user_id
            $validated['update_user_id'] = auth()->id();

            // Manejar campos limpiados
            $validated['calle'] = $request->has('calle') ? $request->calle : 
                (CatalogoCalle::find($request->id_calle)?->Nomvial ?? 'Sin definir');
            
            $validated['y_calle'] = $request->has('y_calle') ? $request->y_calle : 
                (CatalogoCalle::find($request->id_y_calle)?->Nomvial ?? 'Sin definir');
            
            $validated['colonia'] = $request->has('colonia') ? $request->colonia : 
                (Colonias::find($request->id_colonia)?->NOMBRE ?? 'Sin definir');

            // Limpiar IDs cuando corresponda
            if ($validated['calle'] === 'Sin definir') $validated['id_calle'] = null;
            if ($validated['y_calle'] === 'Sin definir') $validated['id_y_calle'] = null;
            if ($validated['colonia'] === 'Sin definir') $validated['id_colonia'] = null;

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
                return '
                    <button class="btn btn-sm btn-primary view-hidrante" data-hidrante-id="'.$hidrante->id.'">
                        Ver <i class="bi bi-eye-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-warning edit-hidrante" data-hidrante-id="'.$hidrante->id.'">
                        Editar <i class="bi bi-pen-fill"></i>
                    </button>
                ';
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
            ->editColumn('fecha_tentativa', function($hidrante) {
                return $hidrante->fecha_tentativa ? $hidrante->fecha_tentativa->format('Y-m-d') : 'N/A';
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
}