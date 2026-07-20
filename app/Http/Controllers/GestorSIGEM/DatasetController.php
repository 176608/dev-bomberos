<?php

namespace App\Http\Controllers\GestorSIGEM;

use Illuminate\Http\Request;
use App\Services\GestorSIGEM\DatasetService;
use App\Services\GestorSIGEM\CategoriaService;
use App\Http\Requests\GestorSIGEM\ProcesarDatasetRequest;

class DatasetController extends Controller
{
    public function __construct(
        private DatasetService $datasetService,
        private CategoriaService $categoriaService,
    ) {
        $this->middleware('auth');
    }

    // ============ STATE ============

    public function estado($id)
    {
        try {
            $data = $this->datasetService->obtenerEstado((int) $id);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // ============ GENERATE ============

    public function generar(Request $request, $id)
    {
        $request->validate([
            'filas' => 'required|integer|min:1|max:50',
            'columnas' => 'required|integer|min:1|max:50',
        ]);

        try {
            $data = $this->datasetService->generarGrilla((int) $id, (int) $request->filas, (int) $request->columnas);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // ============ TREE CRUD ============

    public function storeRaiz(Request $request, $id)
    {
        $request->validate([
            'eje' => 'required|in:vertical,horizontal',
            'nombre' => 'nullable|string|max:255',
            'tipo' => 'nullable|in:dato,pivote,total,promedio,porcentual',
        ]);

        try {
            $data = $this->datasetService->agregarRaiz((int) $id, $request->eje, $request->nombre ?? '', $request->tipo ?? 'dato');
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeHijo(Request $request, $id, $padre)
    {
        try {
            $data = $this->datasetService->agregarHijo((int) $id, (int) $padre);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeHermano(Request $request, $id, $categoria)
    {
        try {
            $data = $this->datasetService->agregarHermano((int) $id, (int) $categoria);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroyCategoria($id, $categoria)
    {
        try {
            $data = $this->datasetService->eliminarCategoria((int) $id, (int) $categoria);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function updateCategoria(Request $request, $id, $categoria)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        try {
            $cat = $this->datasetService->renombrarCategoria((int) $categoria, $request->nombre);
            return response()->json(['success' => true, 'categoria' => $cat]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function updateTipoCategoria(Request $request, $id, $categoria)
    {
        $request->validate(['tipo' => 'required|in:dato,pivote,total,promedio,porcentual']);

        try {
            $cat = $this->datasetService->cambiarTipoCategoria((int) $categoria, $request->tipo);
            return response()->json(['success' => true, 'categoria' => $cat]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function reordenar(Request $request, $id, $categoria)
    {
        $request->validate(['posicion' => 'required|integer|min:1']);

        try {
            $data = $this->datasetService->reordenar((int) $id, (int) $categoria, (int) $request->posicion);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // ============ LEGACY ROW/COLUMN OPERATIONS ============

    public function storeFila($id)
    {
        try {
            $data = $this->datasetService->agregarFila((int) $id);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroyFila($id, $categoria)
    {
        try {
            $data = $this->datasetService->eliminarFila((int) $id, (int) $categoria);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeColumna($id)
    {
        try {
            $data = $this->datasetService->agregarColumna((int) $id);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroyColumna($id, $categoria)
    {
        try {
            $data = $this->datasetService->eliminarColumna((int) $id, (int) $categoria);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // ============ CELL DATA ============

    public function updateCelda(Request $request, $id, $dato)
    {
        $request->validate(['valor' => 'nullable|string']);

        try {
            $dato = $this->datasetService->actualizarCelda((int) $dato, $request->valor ?? '');
            return response()->json(['success' => true, 'dato' => $dato]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function updateCeldaPorCruze(Request $request, $id)
    {
        $request->validate([
            'cat_vertical_id' => 'required|integer',
            'cat_horizontal_id' => 'required|integer',
            'valor' => 'nullable|string',
        ]);

        try {
            $dato = $this->datasetService->actualizarCeldaPorCruze(
                (int) $id,
                (int) $request->cat_vertical_id,
                (int) $request->cat_horizontal_id,
                $request->valor ?? ''
            );
            return response()->json(['success' => true, 'dato' => $dato]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // ============ PASTE ============

    public function paste(Request $request, $id)
    {
        $request->validate([
            'grid' => 'required|array',
            'grid.*' => 'required|array',
        ]);

        try {
            $data = $this->datasetService->pasteGrid((int) $id, $request->grid);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // ============ CLEANUP ============

    public function limpiar($id)
    {
        try {
            $data = $this->datasetService->limpiarDataset((int) $id);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // ============ IMPORT ============

    public function importar(ProcesarDatasetRequest $request, $id)
    {
        try {
            $file = $request->file('dataset');
            $ext = strtolower($file->getClientOriginalExtension());

            if (in_array($ext, ['csv', 'txt'])) {
                $data = $this->datasetService->importarCsv((int) $id, $file);
            } else {
                throw new \RuntimeException('La importación de XLSX estará disponible próximamente. Use formato CSV por ahora.');
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
