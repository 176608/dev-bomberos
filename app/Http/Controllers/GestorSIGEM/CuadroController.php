<?php

namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Controllers\GestorSIGEM\Controller;
use Illuminate\Http\Request;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\CuadroCategoria;
use App\Models\SIGEM\CuadroDato;
use App\Models\SIGEM\SubtemaV2;

class CuadroController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['catalogoPublico']);
    }

    public function index()
    {
        $cuadros = Cuadro::obtenerTodos();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadros',
            'cuadros' => $cuadros
        ]);
    }

    public function create()
    {
        $subtemas = SubtemaV2::obtenerTodos();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadro_form',
            'subtemas' => $subtemas
        ]);
    }

    public function store(Request $request)
    {
        $validados = $request->validate([
            'subtema_id' => 'required|integer|exists:subtema_v2,subtema_id',
            'codigo_cuadro' => 'required|string|max:50',
            'c_titulo' => 'required|string|max:255',
            'c_subtitulo' => 'nullable|string|max:255',
            'publicado' => 'nullable|boolean',
            'tipo_mapa_pdf' => 'nullable|boolean',
            'permite_grafica' => 'nullable|boolean',
            'tipos_grafica_permitida' => 'nullable|json',
            'cabecera_gen' => 'nullable|string',
            'piepagina_gen' => 'nullable|string',
        ]);

        $validados['publicado'] = $request->boolean('publicado');
        $validados['tipo_mapa_pdf'] = $request->boolean('tipo_mapa_pdf');
        $validados['permite_grafica'] = $request->boolean('permite_grafica');

        $cuadro = Cuadro::crear($validados);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Cuadro {$cuadro->codigo_cuadro} creado correctamente."]);
        }

        return redirect()->route('sgiem.admin.cuadros-v2.index')
            ->with('success', "Cuadro {$cuadro->codigo_cuadro} creado correctamente.");
    }

    public function show($id)
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros-v2.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadro_show',
            'cuadro' => $cuadro
        ]);
    }

    public function edit($id)
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros-v2.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        $subtemas = SubtemaV2::obtenerTodos();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadro_form',
            'cuadro' => $cuadro,
            'subtemas' => $subtemas
        ]);
    }

    public function update(Request $request, $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cuadro no encontrado.'], 404);
            }
            return redirect()->route('sgiem.admin.cuadros-v2.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        $validados = $request->validate([
            'subtema_id' => 'sometimes|integer|exists:subtema_v2,subtema_id',
            'codigo_cuadro' => 'sometimes|string|max:50',
            'c_titulo' => 'sometimes|string|max:255',
            'c_subtitulo' => 'nullable|string|max:255',
            'publicado' => 'nullable|boolean',
            'tipo_mapa_pdf' => 'nullable|boolean',
            'permite_grafica' => 'nullable|boolean',
            'tipos_grafica_permitida' => 'nullable|json',
            'cabecera_gen' => 'nullable|string',
            'piepagina_gen' => 'nullable|string',
        ]);

        $validados['publicado'] = $request->boolean('publicado');
        $validados['tipo_mapa_pdf'] = $request->boolean('tipo_mapa_pdf');
        $validados['permite_grafica'] = $request->boolean('permite_grafica');

        $cuadro->actualizar($validados);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Metadatos actualizados correctamente.']);
        }

        return redirect()->route('sgiem.admin.cuadros-v2.index')
            ->with('success', "Cuadro {$cuadro->codigo_cuadro} actualizado.");
    }

    public function destroy($id)
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros-v2.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        $codigo = $cuadro->codigo_cuadro;
        $cuadro->eliminar();

        return redirect()->route('sgiem.admin.cuadros-v2.index')
            ->with('success', "Cuadro {$codigo} eliminado.");
    }

    public function procesarDataset(Request $request, $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros-v2.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        $request->validate([
            'dataset' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        // TODO: Fase 2 — implementar DatasetParser
        // $parser = new \App\Services\SGIEM\DatasetParser($request->file('dataset'));
        // $parser->procesar($cuadro);

        return redirect()->route('sgiem.admin.cuadros-v2.interpretacion', $id)
            ->with('success', 'Dataset cargado. Revisa la interpretación.');
    }

    public function editarInterpretacion($id)
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros-v2.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.interpretacion',
            'cuadro' => $cuadro
        ]);
    }

    public function actualizarCategorias(Request $request, $id)
    {
        // TODO: Fase 2 — validar y actualizar jerarquía de categorías
        return redirect()->route('sgiem.admin.cuadros-v2.interpretacion', $id)
            ->with('success', 'Categorías actualizadas.');
    }

    public function actualizarDato(Request $request, $id, $datoId)
    {
        $dato = CuadroDato::find($datoId);

        if (!$dato || $dato->cuadro_id != $id) {
            return response()->json(['error' => 'Dato no encontrado.'], 404);
        }

        $validados = $request->validate([
            'valor' => 'nullable|string|max:100',
        ]);

        $dato->update($validados);

        return response()->json(['success' => true, 'dato' => $dato]);
    }

    public function agregarCategoria(Request $request, $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros-v2.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        $validados = $request->validate([
            'eje' => 'required|in:horizontal,vertical',
            'padre_id' => 'nullable|integer|exists:cuadro_categoria,categoria_id',
            'nombre' => 'required|string|max:255',
            'orden' => 'required|integer|min:0',
            'tipo' => 'required|in:dato,total,promedio,porcentual',
        ]);

        $validados['cuadro_id'] = $cuadro->cuadro_id;
        CuadroCategoria::create($validados);

        return redirect()->route('sgiem.admin.cuadros-v2.interpretacion', $id)
            ->with('success', 'Categoría agregada.');
    }

    public function catalogoPublico()
    {
        $cuadros = Cuadro::publicados()->with(['subtema.tema'])->get();
        return response()->json($cuadros);
    }

    public function togglePublicado($id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro) {
            return response()->json(['success' => false, 'error' => 'Cuadro no encontrado.'], 404);
        }
        $cuadro->publicado = !$cuadro->publicado;
        $cuadro->save();
        return response()->json([
            'success' => true,
            'publicado' => $cuadro->publicado
        ]);
    }

    public function datosJson($id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro) {
            return response()->json(['error' => 'Cuadro no encontrado.'], 404);
        }
        return response()->json($cuadro);
    }
}
