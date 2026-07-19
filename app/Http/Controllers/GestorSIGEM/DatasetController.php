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

    public function limpiar($id)
    {
        try {
            $data = $this->datasetService->limpiarDataset((int) $id);
            return response()->json(['success' => true, 'data' => $data]);
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
