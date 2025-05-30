<?php

namespace App\Http\Controllers;

use App\Models\Hidrante;
use App\Models\Colonias;
use App\Models\Calles;
use App\Models\CatalogoCalle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalistaController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Analista') {
            return redirect()->route('dashboard');
        }

        $hidrantes = Hidrante::with(['coloniaLocacion', 'callePrincipal', 'calleSecundaria'])->get();
        $calles = CatalogoCalle::all();
        $colonias = Colonias::all();
        
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
        $validated = $request->validate([
            'numero_estacion' => 'required|string',
            'numero_hidrante' => 'required|integer',
            'id_calle' => 'required|integer',
            'id_y_calle' => 'nullable|integer',
            'id_colonia' => 'required|integer',
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

        // Add update_user_id automatically
        $validated['update_user_id'] = auth()->id();

        // Update text fields based on selected IDs
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

        try {
            $hidrante->update($validated);
            return redirect()->route('analista.panel')
                ->with('success', 'Hidrante actualizado exitosamente');
        } catch (\Exception $e) {
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