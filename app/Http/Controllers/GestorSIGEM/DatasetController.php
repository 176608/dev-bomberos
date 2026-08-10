<?php

namespace App\Http\Controllers\GestorSIGEM;

use Illuminate\Http\Request;
use App\Services\GestorSIGEM\DatasetService;

use Illuminate\Support\Facades\Cache;

class DatasetController extends Controller
{
    public function __construct(
        private DatasetService $datasetService,
    ) {
        $this->middleware('auth');
    }

    private function invalidarCacheVisor(int $cuadroId): void
    {
        Cache::forget("visor_cuadro_estado_{$cuadroId}");

        $seccionIds = \App\Models\SIGEM\CuadroSeccion::where('cuadro_id', $cuadroId)->pluck('seccion_id');
        foreach ($seccionIds as $seccionId) {
            Cache::forget("visor_cuadro_{$cuadroId}_seccion_{$seccionId}");
        }
    }

    public function estado($id)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->obtenerEstado((int) $id)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function generar(Request $request, $id)
    {
        $request->validate(['filas' => 'required|integer|min:1|max:50', 'columnas' => 'required|integer|min:1|max:50']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->generarGrilla((int) $id, (int) $request->filas, (int) $request->columnas)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeFila(Request $request, $id)
    {
        $nombre = $request->input('nombre');
        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->agregarFila((int) $id, $nombre)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroyFila($id, $categoria)
    {
        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->eliminarFila((int) $id, (int) $categoria)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeColumna(Request $request, $id)
    {
        $nombre = $request->input('nombre');
        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->agregarColumna((int) $id, $nombre)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroyColumna($id, $categoria)
    {
        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->eliminarColumna((int) $id, (int) $categoria)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function updateCelda(Request $request, $id, $dato)
    {
        $request->validate(['valor' => 'nullable|string']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'dato' => $this->datasetService->actualizarCelda((int) $id, (int) $dato, $request->valor ?? '')]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function updateCategoria(Request $request, $id, $categoria)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'categoria' => $this->datasetService->renombrarCategoria((int) $id, (int) $categoria, $request->nombre)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function paste(Request $request, $id)
    {
        $request->validate([
            'grid' => 'required|array',
            'grid.*' => 'required|array',
            'start_vertical_id' => 'nullable|integer',
            'start_horizontal_id' => 'nullable|integer',
            'verticales' => 'nullable|array',
            'verticales.*' => 'integer',
            'horizontales' => 'nullable|array',
            'horizontales.*' => 'integer',
            'seccion_id' => 'nullable|integer',
        ]);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->pasteGrid(
                (int) $id, $request->grid,
                $request->start_vertical_id, $request->start_horizontal_id,
                $request->verticales, $request->horizontales,
                $request->seccion_id
            )]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function cloneCategoria(Request $request, $id, $categoria)
    {
        $nombre = $request->input('nombre');
        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->clonarCategoria((int) $id, (int) $categoria, $nombre)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function storeHijo(Request $request, $id)
    {
        $request->validate(['padre_id' => 'required|integer']);

        $nombre = $request->input('nombre');
        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->agregarHijo((int) $id, (int) $request->padre_id, $nombre)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function pasteCategorias(Request $request, $id)
    {
        $request->validate([
            'eje' => 'required|in:vertical,horizontal',
            'start_categoria_id' => 'required|integer',
            'valores' => 'required|array',
            'valores.*' => 'string',
        ]);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->pasteCategorias(
                (int) $id, $request->eje, (int) $request->start_categoria_id, $request->valores
            )]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function limpiarDatos(Request $request, $id)
    {
        try {
            $seccion_id = (int) ($request->query('seccion_id', $request->input('seccion_id', 0)));
            if (!$seccion_id) throw new \RuntimeException('seccion_id requerido');
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->limpiarDatos((int) $id, $seccion_id)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function storeSeccion(Request $request, $id)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->agregarSeccion((int) $id, $request->nombre)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateSeccion(Request $request, $id, $seccion)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->actualizarSeccion(
                (int) $id, (int) $seccion, $request->nombre, $request->header, $request->footer
            )]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroySeccion($id, $seccion)
    {
        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->eliminarSeccion((int) $id, (int) $seccion)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function switchSeccion($id, $seccion)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->switchSeccion((int) $id, (int) $seccion)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function reordenarSeccion(Request $request, $id, $seccion)
    {
        $request->validate(['direccion' => 'required|in:up,down']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->reordenarSeccion((int) $id, (int) $seccion, $request->direccion)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function importables(Request $request, $id)
    {
        try {
            $temaId = $request->integer('tema_id', null) ?: null;
            $subtemaId = $request->integer('subtema_id', null) ?: null;

            $cuadros = $this->datasetService->obtenerImportables((int) $id, $temaId, $subtemaId);

            $result = $cuadros->map(fn($c) => [
                'cuadro_id' => $c->cuadro_id,
                'codigo' => $c->codigo_cuadro,
                'titulo' => $c->c_titulo,
                'publicado' => $c->publicado,
                'tema' => $c->subtema?->tema?->tema_titulo,
                'tema_id' => $c->subtema?->tema?->tema_id,
                'subtema' => $c->subtema?->subtema_titulo,
                'subtema_id' => $c->subtema?->subtema_id,
                'pivot_label' => $c->pivot_label,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function importarEstructura(Request $request, $id)
    {
        $request->validate(['origen_cuadro_id' => 'required|integer']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->importarEstructura((int) $id, (int) $request->origen_cuadro_id)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function reordenarCategoria(Request $request, $id, $categoria)
    {
        $request->validate(['direccion' => 'required|in:up,down']);

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->reordenarCategoria((int) $id, (int) $categoria, $request->direccion)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updatePivot(Request $request, $id)
    {
        $request->validate(['label' => 'required|string|max:100']);

        try {
            $cuadro = \App\Models\SIGEM\Cuadro::obtenerPorId((int) $id);
            if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

            $cuadro->actualizar(['pivot_label' => $request->label]);
            $this->invalidarCacheVisor((int) $id);

            return response()->json(['success' => true]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function regenerar(Request $request, $id)
    {
        $pivot = $request->input('pivot_label', 'PIVOTE');

        try {
            $this->invalidarCacheVisor((int) $id);
            return response()->json(['success' => true, 'data' => $this->datasetService->regenerarDataset((int) $id, $pivot)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateTiposGrafica(Request $request, $id)
    {
        $request->validate(['tipos' => 'required|array']);
        $request->validate(['tipos.*' => 'string|in:bar,line,pie,doughnut,radar,polarArea,scatter,bubble']);

        try {
            $cuadro = \App\Models\SIGEM\Cuadro::obtenerPorId((int) $id);
            if (!$cuadro) throw new \RuntimeException('Cuadro no encontrado');

            $cuadro->actualizar(['tipos_grafica_permitida' => $request->tipos]);

            $this->invalidarCacheVisor((int) $id);

            return response()->json(['success' => true, 'tipos' => $request->tipos]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
