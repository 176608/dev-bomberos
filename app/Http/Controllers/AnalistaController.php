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
            'fecha_alta' => 'required|date',
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
            'numero_estacion' => 'required|integer',
            'numero_hidrante' => 'nullable|integer',
            'id_calle' => 'required|integer',
            'id_y_calle' => 'required|integer',
            'id_colonia' => 'required|integer',
            'llave_hidrante' => 'nullable|string',
            'presion_agua' => 'nullable|string',
            'color' => 'nullable|string',
            'llave_fosa' => 'nullable|string',
            'ubicacion_fosa' => 'nullable|string',
            'hidrante_conectado_tubo' => 'nullable|string',
            'estado_hidrante' => 'nullable|string',
            'marca' => 'nullable|string',
            'anio' => 'nullable|integer',
            'observaciones' => 'nullable|string',
            'oficial' => 'required|string'
        ]);

        // Add update_user_id automatically
        $validated['update_user_id'] = auth()->id();

        // Update related text fields based on IDs
        $calle = CatalogoCalle::find($request->id_calle);
        $yCalle = CatalogoCalle::find($request->id_y_calle);
        $colonia = Colonias::find($request->id_colonia);

        $validated['calle'] = $calle ? $calle->Nomvial : null;
        $validated['y_calle'] = $yCalle ? $yCalle->Nomvial : null;
        $validated['colonia'] = $colonia ? $colonia->NOMBRE : null;

        $hidrante->update($validated);

        return redirect()->route('analista.panel')
            ->with('success', 'Hidrante actualizado exitosamente');
    }
}