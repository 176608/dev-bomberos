<?php

namespace App\Http\Controllers\GestorSIGEM;

use Illuminate\Http\Request;
use App\Http\Requests\GestorSIGEM\StoreCuadroV2Request;
use App\Http\Requests\GestorSIGEM\StoreCategoriaRequest;
use App\Http\Requests\GestorSIGEM\ProcesarDatasetRequest;
use App\Services\GestorSIGEM\CuadroV2Service;
use App\Services\GestorSIGEM\CategoriaService;
use App\Services\GestorSIGEM\DatasetService;

class CuadroV2Controller extends Controller
{
    public function __construct(
        private CuadroV2Service $cuadroV2Service,
        private CategoriaService $categoriaService,
        private DatasetService $datasetService,
    ) {
        $this->middleware('auth')->except(['catalogoPublico']);
    }

    public function index()
    {
        $data = $this->cuadroV2Service->listar();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadros',
            'cuadros' => $data['cuadros'],
            'temas' => $data['temas'],
        ]);
    }

    public function create()
    {
        $subtemas = $this->cuadroV2Service->obtenerSubtemasParaFormulario();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadro_form',
            'subtemas' => $subtemas,
        ]);
    }

    public function store(StoreCuadroV2Request $request)
    {
        $cuadro = $this->cuadroV2Service->crear($request->validated());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Cuadro {$cuadro->codigo_cuadro} creado correctamente."]);
        }

        return redirect()->route('sgiem.admin.cuadros.index')
            ->with('success', "Cuadro {$cuadro->codigo_cuadro} creado correctamente.");
    }

    public function show($id)
    {
        $cuadro = $this->cuadroV2Service->obtenerPorId((int) $id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadro_show',
            'cuadro' => $cuadro,
        ]);
    }

    public function datasetManage($id)
    {
        $cuadro = $this->cuadroV2Service->obtenerPorId((int) $id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        try {
            $estado = $this->datasetService->obtenerEstado((int) $id);
        } catch (\RuntimeException) {
            $estado = [
                'tiene_dataset' => false,
                'verticales' => [],
                'horizontales' => [],
                'tabla' => [],
                'max_filas' => 0,
                'max_columnas' => 0,
            ];
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.dataset_manage',
            'cuadro' => $cuadro,
            'estadoInicial' => $estado,
        ]);
    }

    public function edit($id)
    {
        $cuadro = $this->cuadroV2Service->obtenerPorId((int) $id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        $subtemas = $this->cuadroV2Service->obtenerSubtemasParaFormulario();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadro_form',
            'cuadro' => $cuadro,
            'subtemas' => $subtemas,
        ]);
    }

    public function update(StoreCuadroV2Request $request, $id)
    {
        try {
            $this->cuadroV2Service->actualizar((int) $id, $request->validated());

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Metadatos actualizados correctamente.']);
            }

            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('success', 'Cuadro actualizado.');

        } catch (\RuntimeException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
            }
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $codigo = $this->cuadroV2Service->eliminar((int) $id);

            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('success', "Cuadro {$codigo} eliminado.");

        } catch (\RuntimeException $e) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', $e->getMessage());
        }
    }

    public function procesarDataset(ProcesarDatasetRequest $request, $id)
    {
        try {
            $this->datasetService->procesar((int) $id, $request->file('dataset'));

            return redirect()->route('sgiem.admin.cuadros.interpretacion', $id)
                ->with('success', 'Dataset cargado. Revisa la interpretación.');

        } catch (\RuntimeException $e) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', $e->getMessage());
        }
    }

    public function editarInterpretacion($id)
    {
        $cuadro = $this->cuadroV2Service->obtenerPorId((int) $id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.interpretacion',
            'cuadro' => $cuadro,
        ]);
    }

    public function actualizarCategorias(Request $request, $id)
    {
        // TODO: Fase 2 — implementar actualización masiva de categorías
        return redirect()->route('sgiem.admin.cuadros.interpretacion', $id)
            ->with('success', 'Categorías actualizadas.');
    }

    public function actualizarDato(Request $request, $id, $datoId)
    {
        try {
            $dato = $this->categoriaService->actualizarDato(
                (int) $id,
                (int) $datoId,
                $request->input('valor', '')
            );

            return response()->json(['success' => true, 'dato' => $dato]);

        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function agregarCategoria(StoreCategoriaRequest $request, $id)
    {
        try {
            $this->categoriaService->agregar((int) $id, $request->validated());

            return redirect()->route('sgiem.admin.cuadros.interpretacion', $id)
                ->with('success', 'Categoría agregada.');

        } catch (\RuntimeException $e) {
            return redirect()->route('sgiem.admin.cuadros.interpretacion', $id)
                ->with('error', $e->getMessage());
        }
    }

    public function catalogoPublico()
    {
        $cuadros = $this->cuadroV2Service->catalogoPublico();
        return response()->json($cuadros);
    }

    public function togglePublicado($id)
    {
        try {
            $publicado = $this->cuadroV2Service->togglePublicado((int) $id);

            return response()->json([
                'success' => true,
                'publicado' => $publicado,
            ]);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 404);
        }
    }

    public function datosJson($id)
    {
        $data = $this->cuadroV2Service->datosJson((int) $id);

        if (!$data) {
            return response()->json(['error' => 'Cuadro no encontrado.'], 404);
        }

        return response()->json($data);
    }
}
