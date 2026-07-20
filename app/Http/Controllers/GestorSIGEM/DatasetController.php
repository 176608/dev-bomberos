<?php

namespace App\Http\Controllers\GestorSIGEM;

use Illuminate\Http\Request;
use App\Services\GestorSIGEM\DatasetService;
use App\Http\Requests\GestorSIGEM\ProcesarDatasetRequest;

class DatasetController extends Controller
{
    public function __construct(
        private DatasetService $datasetService,
    ) {
        $this->middleware('auth');
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
            return response()->json(['success' => true, 'data' => $this->datasetService->generarGrilla((int) $id, (int) $request->filas, (int) $request->columnas)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeFila($id)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->agregarFila((int) $id)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroyFila($id, $categoria)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->eliminarFila((int) $id, (int) $categoria)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function storeColumna($id)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->agregarColumna((int) $id)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function destroyColumna($id, $categoria)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->eliminarColumna((int) $id, (int) $categoria)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function updateCelda(Request $request, $id, $dato)
    {
        $request->validate(['valor' => 'nullable|string']);

        try {
            return response()->json(['success' => true, 'dato' => $this->datasetService->actualizarCelda((int) $dato, $request->valor ?? '')]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function updateCategoria(Request $request, $id, $categoria)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        try {
            return response()->json(['success' => true, 'categoria' => $this->datasetService->renombrarCategoria((int) $categoria, $request->nombre)]);
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
        ]);

        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->pasteGrid(
                (int) $id, $request->grid,
                $request->start_vertical_id, $request->start_horizontal_id
            )]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function limpiar($id)
    {
        try {
            return response()->json(['success' => true, 'data' => $this->datasetService->limpiarDataset((int) $id)]);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function importar(ProcesarDatasetRequest $request, $id)
    {
        try {
            $file = $request->file('dataset');
            $ext = strtolower($file->getClientOriginalExtension());

            if (in_array($ext, ['csv', 'txt'])) {
                return response()->json(['success' => true, 'data' => $this->datasetService->importarCsv((int) $id, $file)]);
            }
            throw new \RuntimeException('Solo CSV por ahora');
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
