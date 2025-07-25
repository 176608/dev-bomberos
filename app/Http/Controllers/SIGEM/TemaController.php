<?php

namespace App\Http\Controllers;

use App\Models\Tema;
use Illuminate\Http\Request;

class TemaController extends Controller
{
    public function index()
    {
        $temas = Tema::all();
        return view('tema.index', compact('temas'));
    }

    public function create()
    {
        return view('tema.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        Tema::create($request->all());

        return redirect()->route('tema.index')->with('success', 'Tema creado correctamente.');
    }

    public function edit($id)
    {
        $tema = Tema::findOrFail($id);
        return view('tema.edit', compact('tema'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        $tema = Tema::findOrFail($id);
        $tema->update($request->all());

        return redirect()->route('tema.index')->with('success', 'Tema actualizado correctamente.');
    }

    public function destroy($id)
    {
        $tema = Tema::findOrFail($id);
        $tema->delete();

        return redirect()->route('tema.index')->with('success', 'Tema eliminado correctamente.');
    }
}
