<?php

namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Requests\GestorSIGEM\StoreCuadroV2Request;
use App\Services\GestorSIGEM\CuadroV2Service;
use App\Services\GestorSIGEM\DatasetService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CuadroExcelExport;

class CuadroV2Controller extends Controller
{
    public function __construct(
        private CuadroV2Service $cuadroV2Service,
        private DatasetService $datasetService,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = $this->cuadroV2Service->listar();
        $maxSecciones = \App\Models\SIGEM\CuadroSeccion::selectRaw('cuadro_id, COUNT(*) as total')
            ->groupBy('cuadro_id')
            ->orderBy('total', 'desc')
            ->limit(1)
            ->value('total') ?? 0;
        $seccionCounts = \App\Models\SIGEM\CuadroSeccion::selectRaw('cuadro_id, COUNT(*) as total')
            ->groupBy('cuadro_id')
            ->pluck('total', 'cuadro_id');
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cuadros',
            'cuadros' => $data['cuadros'],
            'temas' => $data['temas'],
            'maxSecciones' => $maxSecciones,
            'seccionCounts' => $seccionCounts,
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
                'vertical_tree' => [],
                'horizontal_tree' => [],
                'headers' => [],
                'labels' => [],
                'data' => [],
                'max_filas' => 0,
                'max_columnas' => 0,
                'secciones' => [],
                'seccion_activa_id' => null,
                'tipos_grafica_permitida' => $cuadro->tipos_grafica_permitida ?: [],
            ];
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.dataset_manage',
            'cuadro' => $cuadro,
            'estadoInicial' => $estado,
        ]);
    }

    public function graficaManage($id)
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
                'vertical_tree' => [],
                'horizontal_tree' => [],
                'headers' => [],
                'labels' => [],
                'data' => [],
                'max_filas' => 0,
                'max_columnas' => 0,
                'secciones' => [],
                'seccion_activa_id' => null,
                'tipos_grafica_permitida' => $cuadro->tipos_grafica_permitida ?: [],
            ];
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.grafica',
            'cuadro' => $cuadro,
            'estadoInicial' => $estado,
        ]);
    }

    public function documentoManage($id)
    {
        $cuadro = $this->cuadroV2Service->obtenerPorId((int) $id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        try {
            $estado = $this->datasetService->obtenerEstado((int) $id);
        } catch (\RuntimeException) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', 'El cuadro no tiene dataset. Debes generar uno antes de gestionar el documento.');
        }

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.documento_manage',
            'cuadro' => $cuadro,
            'estadoInicial' => $estado,
        ]);
    }

    public function exportarDocumento($id)
    {
        $cuadro = $this->cuadroV2Service->obtenerPorId((int) $id);

        if (!$cuadro) {
            return redirect()->route('sgiem.admin.cuadros.index')
                ->with('error', 'Cuadro no encontrado.');
        }

        try {
            $secciones = $cuadro->secciones()->orderBy('orden')->get();
            $estado = $this->datasetService->obtenerEstado((int) $id, $secciones->isNotEmpty() ? $secciones->first()->seccion_id : null);
        } catch (\RuntimeException) {
            return redirect()->route('sgiem.admin.cuadros.documento', $id)
                ->with('error', 'El cuadro no tiene dataset. Debes generar uno antes de exportar.');
        }

        $fecha = now()->format('d_m_Y');
        $nombre = $cuadro->codigo_cuadro . '_' . $fecha . '.xlsx';

        $export = new CuadroExcelExport(
            estado: $estado,
            codigoCuadro: $cuadro->codigo_cuadro,
            tituloCuadro: $cuadro->c_titulo,
            subtituloCuadro: $cuadro->c_subtitulo ?? '',
            piePagina: $cuadro->pie_pagina,
        );

        return Excel::download($export, $nombre);
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

            if ($request->boolean('eliminar_dataset')) {
                $this->datasetService->eliminarDataset((int) $id);
            }

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
