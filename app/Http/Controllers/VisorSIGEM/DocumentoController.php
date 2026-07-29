<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Http\Controllers\Controller;
use App\Models\SIGEM\Cuadro;
use App\Services\GestorSIGEM\DatasetService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CuadroExcelExport;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function __construct(
        private DatasetService $datasetService,
    ) {}

    public function exportarExcel(int $id, Request $request)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro || !$cuadro->publicado) {
            abort(404);
        }

        if ($cuadro->tipo_mapa_pdf) {
            return redirect()->route('sigem.v2.cuadro.mapa', $id)
                ->with('error', 'Los cuadros tipo mapa no tienen exportación Excel.');
        }

        try {
            $secciones = $cuadro->secciones()->orderBy('orden')->get();
        } catch (\Throwable) {
            abort(500, 'Error al cargar secciones');
        }

        $todo = $request->boolean('todo', false);

        $seccionesData = [];
        if ($secciones->isEmpty()) {
            try {
                $estado = $this->datasetService->obtenerEstado($id);
            } catch (\RuntimeException) {
                abort(500, 'El cuadro no tiene dataset.');
            }
            if (!$todo) $estado = $this->aplicarFiltrosDesdeUrl($estado, $request);
            $seccionesData[] = [
                'seccion' => ['seccion_id' => null, 'nombre' => 'Serie única'],
                'estado' => $estado,
            ];
        } else {
            foreach ($secciones as $sec) {
                try {
                    $estado = $this->datasetService->obtenerEstado($id, $sec->seccion_id);
                } catch (\RuntimeException) {
                    continue;
                }
                if (!$todo) $estado = $this->aplicarFiltrosDesdeUrl($estado, $request);
                $seccionesData[] = [
                    'seccion' => $sec->toArray(),
                    'estado' => $estado,
                ];
            }
        }

        if (empty($seccionesData)) {
            abort(500, 'No hay datos para exportar.');
        }

        $fecha = now()->format('d_m_Y');
        $nombre = $cuadro->codigo_cuadro . '_' . $fecha . '.xlsx';

        $export = new CuadroExcelExport(
            codigoCuadro: $cuadro->codigo_cuadro,
            tituloCuadro: $cuadro->c_titulo,
            subtituloCuadro: $cuadro->c_subtitulo ?? '',
            piePagina: $cuadro->pie_pagina,
            seccionesData: $seccionesData,
            mostrarLogo: true,
        );

        $storeName = 'excel_' . uniqid() . '.xlsx';
        Excel::store($export, $storeName);
        $fullPath = storage_path('app/' . $storeName);
        return response()->download($fullPath, $nombre)->deleteFileAfterSend(true);
    }

    private function aplicarFiltrosDesdeUrl(array $estado, Request $request): array
    {
        $visiblesV = $this->parseIdList($request->query('v', ''));
        $visiblesH = $this->parseIdList($request->query('h', ''));
        $visiblesS = $this->parseIdList($request->query('s', ''));

        if (!empty($visiblesV)) {
            $estado['verticales'] = array_values(array_filter($estado['verticales'], fn($v) => in_array($v['categoria_id'], $visiblesV)));
        }
        if (!empty($visiblesH)) {
            $estado['horizontales'] = array_values(array_filter($estado['horizontales'], fn($h) => in_array($h['categoria_id'], $visiblesH)));
        }

        return $estado;
    }

    private function parseIdList(?string $raw): array
    {
        if (!$raw) return [];
        $ids = [];
        foreach (explode(',', $raw) as $part) {
            $id = (int) ltrim($part, '-');
            if ($id > 0) $ids[] = $id;
        }
        return $ids;
    }
}
