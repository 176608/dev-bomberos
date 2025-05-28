<?php

namespace App\Http\Controllers;

use App\Models\Hidrante;
use App\Models\Colonias;
use App\Models\Calles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalistaController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Analista') {
            return redirect()->route('dashboard');
        }

        $hidrantes = Hidrante::all();
        $cat_colonias = Colonias::all();
        $cat_calles = Calles::all();
        return view('roles.analista', compact('hidrantes', 'cat_colonias', 'cat_calles'));
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
        $request->validate([
            'fecha_alta' => 'required|date',
            'colonia' => 'required|string',
            'id_colonia' => 'required|integer',
            'calle' => 'required|string',
            'y_calle' => 'required|string',
            'oficial' => 'required|boolean'
        ]);

        $hidrante->update($request->all());
        return redirect()->route('analista.panel')->with('success', 'Hidrante actualizado exitosamente');
    }
}