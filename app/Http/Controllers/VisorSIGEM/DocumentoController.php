<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Services\GestorSIGEM\DatasetService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CuadroExcelExport;
use Illuminate\Http\Request;

class DocumentoController extends \App\Http\Controllers\VisorSIGEM\Controller
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

        $parsedS = $this->parseIdListSigned($request->query('s', ''));
        if ($parsedS !== null) {
            $secIds = $parsedS['ids'];
            $isExceptions = $parsedS['isExceptions'];
            $secciones = $secciones->filter(fn($s) => $isExceptions ? !in_array($s->seccion_id, $secIds) : in_array($s->seccion_id, $secIds));
        }

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

        return Excel::download($export, $nombre);
    }

    private function aplicarFiltrosDesdeUrl(array $estado, Request $request): array
    {
        $parsedV = $this->parseIdListSigned($request->query('v', ''));
        $parsedH = $this->parseIdListSigned($request->query('h', ''));

        if ($parsedV !== null) {
            $ids = $parsedV['ids'];
            $isExceptions = $parsedV['isExceptions'];
            $visibleIds = [];
            foreach ($estado['verticales'] as $v) {
                $visible = $isExceptions ? !in_array($v['categoria_id'], $ids) : in_array($v['categoria_id'], $ids);
                if ($visible) $visibleIds[] = $v['categoria_id'];
            }
            $estado['_visibleVerticalIds'] = $visibleIds;
        }
        if ($parsedH !== null) {
            $ids = $parsedH['ids'];
            $isExceptions = $parsedH['isExceptions'];
            $visibleIds = [];
            foreach ($estado['horizontales'] as $h) {
                $visible = $isExceptions ? !in_array($h['categoria_id'], $ids) : in_array($h['categoria_id'], $ids);
                if ($visible) $visibleIds[] = $h['categoria_id'];
            }
            $estado['_visibleHorizontalIds'] = $visibleIds;
        }

        return $estado;
    }

    private function parseIdListSigned(?string $raw): ?array
    {
        if (!$raw) return null;
        $ids = [];
        $isExceptions = false;
        foreach (explode(',', $raw) as $part) {
            $part = trim($part);
            if ($part === '') continue;
            if ($part[0] === '-') {
                $isExceptions = true;
                $ids[] = (int) substr($part, 1);
            } else {
                $ids[] = (int) $part;
            }
        }
        if (empty($ids)) return null;
        return ['ids' => $ids, 'isExceptions' => $isExceptions];
    }
}
