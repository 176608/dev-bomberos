<?php

namespace App\Http\Controllers;

use App\Models\Hidrante;
use App\Models\Colonias;
use App\Models\Calles;
use App\Models\CatalogoCalle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class AnalistaController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Analista') {
            return redirect()->route('dashboard');
        }

        // Eager loading con latest() para ordenar por los más recientes
        $hidrantes = Hidrante::with(['coloniaLocacion', 'callePrincipal', 'calleSecundaria'])
            ->latest('id')
            ->get();
        
        // Cache de datos frecuentemente usados
        $calles = Cache::remember('calles', 3600, function () {
            return CatalogoCalle::all();
        });
        
        $colonias = Cache::remember('colonias', 3600, function () {
            return Colonias::all();
        });
        
        return view('roles.analista', compact('hidrantes', 'calles', 'colonias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_inspeccion' => 'required|date',
            'colonia' => 'required|string',
            'id_colonia' => 'required|integer',
            'calle' => 'required|string',
            'y_calle' => 'required|string',
            'oficial' => 'required|boolean'
        ]);

        Hidrante::create($request->all());
        return redirect()->route('analista.panel')->with('success', 'Hidrante creado exitosamente');
    }

    public function update(Request $request, Hidrante $hidrante)
    {
        try {
            \DB::beginTransaction();

            $validated = $request->validate([
                'numero_estacion' => 'required|string',
                'numero_hidrante' => 'required|integer',
                'id_calle' => 'required|integer',
                'id_y_calle' => 'nullable|integer',
                'id_colonia' => 'nullable|integer',
                'llave_hidrante' => 'required|string',
                'presion_agua' => 'required|string',
                'color' => 'nullable|string',
                'llave_fosa' => 'required|string',
                'ubicacion_fosa' => 'nullable|string',
                'hidrante_conectado_tubo' => 'required|string',
                'estado_hidrante' => 'required|string',
                'marca' => 'nullable|string',
                'anio' => 'nullable|integer',
                'observaciones' => 'nullable|string',
                'oficial' => 'required|string'
            ]);

            // Log before update
            \Log::info('Updating hidrante:', [
                'id' => $hidrante->id,
                'old_data' => $hidrante->toArray(),
                'new_data' => $validated
            ]);

            $validated['update_user_id'] = auth()->id();

            // Update related text fields
            if ($request->id_calle) {
                $calle = CatalogoCalle::find($request->id_calle);
                $validated['calle'] = $calle ? $calle->Nomvial : '';
            }

            if ($request->id_y_calle) {
                $yCalle = CatalogoCalle::find($request->id_y_calle);
                $validated['y_calle'] = $yCalle ? $yCalle->Nomvial : '';
            }

            if ($request->id_colonia) {
                $colonia = Colonias::find($request->id_colonia);
                $validated['colonia'] = $colonia ? $colonia->NOMBRE : '';
            }

            $hidrante->update($validated);
            
            \DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('analista.panel')
                ->with('success', 'Hidrante actualizado exitosamente');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating hidrante:', [
                'id' => $hidrante->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('analista.panel')
                ->with('error', 'Error al actualizar el hidrante: ' . $e->getMessage());
        }
    }

    public function edit(Hidrante $hidrante)
    {
        try {
            $calles = CatalogoCalle::all();
            $colonias = Colonias::all();
            
            // Renderizar solo el contenido del modal
            return view('partials.hidrante-form', [
                'hidrante' => $hidrante,
                'calles' => $calles,
                'colonias' => $colonias,
                'modalId' => $hidrante->id
            ])->render();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar los datos del hidrante'
            ], 500);
        }
    }
}