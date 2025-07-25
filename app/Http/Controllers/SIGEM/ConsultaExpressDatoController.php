<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultaExpressDato;

class ConsultaExpressDatoController extends Controller
{
    // Actualiza el valor de un concepto existente
    public function update(Request $request, $id)
    {
        $dato = ConsultaExpressDato::findOrFail($id);
        $dato->valor = $request->input('valor');
        $dato->save();

        return redirect()->back()->with('success', 'Dato actualizado correctamente');
    }

    // Agrega un nuevo dato a un contenido existente
    public function store(Request $request)
    {
        $request->validate([
            'ce_contenido_id' => 'required|exists:consulta_express_contenido,ce_contenido_id',
            'concepto' => 'required|string|max:255',
            'valor' => 'required|numeric',
        ]);

        ConsultaExpressDato::create([
            'ce_contenido_id' => $request->ce_contenido_id,
            'concepto' => $request->concepto,
            'valor' => $request->valor,
        ]);

        return redirect()->back()->with('success', 'Nuevo concepto agregado correctamente');
    }
}
