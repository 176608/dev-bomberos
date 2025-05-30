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

        // Eager loading con latest() y selección específica de campos
        $hidrantes = Hidrante::with([
            'coloniaLocacion:IDKEY,NOMBRE', 
            'callePrincipal:IDKEY,Nomvial', 
            'calleSecundaria:IDKEY,Nomvial'
        ])
        ->latest('id')
        ->get();

        // Seleccionar solo los campos necesarios
        $calles = CatalogoCalle::select('IDKEY', 'Nomvial')
            ->orderBy('Nomvial')
            ->get();
        
        $colonias = Colonias::select('IDKEY', 'NOMBRE')
            ->orderBy('NOMBRE')
            ->get();
        
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

            // Add update_user_id
            $validated['update_user_id'] = auth()->id();

            // Update related text fields
            $validated['calle'] = CatalogoCalle::find($request->id_calle)?->Nomvial ?? '';
            $validated['y_calle'] = CatalogoCalle::find($request->id_y_calle)?->Nomvial ?? '';
            $validated['colonia'] = Colonias::find($request->id_colonia)?->NOMBRE ?? '';

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
}