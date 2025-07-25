<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubtemaController extends Controller
{
    // Mostrar listado de subtemas
    public function index()
    {
        $subtemas = DB::table('consulta_express_subtema as s')
            ->leftJoin('consulta_express_tema as t', 's.ce_tema_id', '=', 't.ce_tema_id')
            ->select('s.*', 't.tema as nombre_tema') // ← usa el nombre real
            ->get();

        return view('subtema.index', compact('subtemas'));
    }

    // Mostrar formulario para crear subtema
    public function create()
    {
        $temas = DB::table('consulta_express_tema')->get();
        return view('subtema.create', compact('temas'));
    }

    // Guardar nuevo subtema
    public function store(Request $request)
    {
        DB::table('consulta_express_subtema')->insert([
            'ce_subtema' => $request->input('ce_subtema'),
            'ce_tema_id' => $request->input('ce_tema_id'),
        ]);

        return redirect()->route('subtema.index')->with('success', 'Subtema agregado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $subtema = DB::table('consulta_express_subtema')->where('ce_subtema_id', $id)->first();
        $temas = DB::table('consulta_express_tema')->get();

        return view('subtema.edit', compact('subtema', 'temas'));
    }

    // Actualizar subtema
    public function update(Request $request, $id)
    {
        DB::table('consulta_express_subtema')
            ->where('ce_subtema_id', $id)
            ->update([
                'ce_subtema' => $request->input('ce_subtema'),
                'ce_tema_id' => $request->input('ce_tema_id'),
            ]);

        return redirect()->route('subtema.index')->with('success', 'Subtema actualizado correctamente.');
    }

    // Eliminar subtema
    public function destroy($id)
    {
        DB::table('consulta_express_subtema')->where('ce_subtema_id', $id)->delete();
        return redirect()->route('subtema.index')->with('success', 'Subtema eliminado correctamente.');
    }
}
