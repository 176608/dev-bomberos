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

class CapturistaController extends Controller
{
    /*public function __construct()
    {
        $this->middleware('auth');
    }*/

    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Capturista') {
            return redirect()->route('dashboard');
        }

        // Obtener hidrantes con sus relaciones
        $hidrantes = Hidrante::with([
            'coloniaLocacion',
            'callePrincipal',
            'calleSecundaria',
            'createdBy',
            'updatedBy'
        ])->get();

        // Obtener calles y colonias para los formularios
        $calles = CatalogoCalle::select('IDKEY', 'Nomvial')->orderBy('Nomvial')->get();
        $colonias = Colonias::select('IDKEY', 'NOMBRE')->orderBy('NOMBRE')->get();
        
        return view('roles.capturista', compact('hidrantes', 'calles', 'colonias'));
    }

    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();

            $validated = $request->validate([
                'fecha_inspeccion' => 'required|date',
                'fecha_tentativa' => 'required|date',
                'numero_estacion' => 'required|string',
                'numero_hidrante' => 'required|integer',
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

            // Agregar IDs de usuario
            $validated['create_user_id'] = auth()->id();
            $validated['update_user_id'] = auth()->id();

            // Manejar campos de ubicación
            if (!$request->id_calle && !$request->id_y_calle && !$request->id_colonia) {
                $validated['calle'] = 'Pendiente';
                $validated['y_calle'] = 'Pendiente';
                $validated['colonia'] = 'Pendiente';
            } else {
                // Lógica existente para obtener nombres de calles y colonia
                $validated['calle'] = $request->id_calle ? 
                    (CatalogoCalle::find($request->id_calle)?->Nomvial ?? '') : '';
                $validated['y_calle'] = $request->id_y_calle ? 
                    (CatalogoCalle::find($request->id_y_calle)?->Nomvial ?? '') : '';
                $validated['colonia'] = $request->id_colonia ? 
                    (Colonias::find($request->id_colonia)?->NOMBRE ?? '') : '';
            }

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
                'numero_hidrante' => 'required|integer',
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

            ConfiguracionCapturista::updateOrCreate(
                ['user_id' => auth()->id()],
                ['configuracion' => $validated['configuracion']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Configuración guardada exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving configuration:', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración'
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
                'message' => 'Error al obtener la configuración'
            ], 500);
        }
    }
}