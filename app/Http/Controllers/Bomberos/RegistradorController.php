<?php

namespace App\Http\Controllers\Bomberos;

use App\Http\Controllers\Bomberos\Controller;
use App\Models\Bomberos\Colonias;
use App\Models\Bomberos\Calles;
use App\Models\Bomberos\CatalogoCalle;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class RegistradorController extends Controller
{
    /**
     * Mostrar el panel principal del registrador
     */
    public function index()
    {
        return view('roles.registrador');
    }

    /**
     * DataTable para Zonas (Colonias)
     */
    public function zonasDataTable(Request $request)
    {
        try {
            $query = Colonias::select(['IDKEY', 'NOMBRE', 'TIPO']);

            return DataTables::eloquent($query)
                ->addColumn('acciones', function($zona) {
                    return '
                        <button class="btn btn-sm btn-warning edit-zona" title="Editar zona" data-zona-id="'.$zona->IDKEY.'">
                            <i class="bi bi-pen"></i>
                        </button>
                    ';
                })
                ->editColumn('NOMBRE', function($zona) {
                    return $zona->NOMBRE ?? 'N/A';
                })
                ->editColumn('TIPO', function($zona) {
                    return $zona->TIPO ?? 'N/A';
                })
                ->rawColumns(['acciones'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Error en zonasDataTable:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error al procesar la solicitud de zonas'
            ], 500);
        }
    }

    /**
     * DataTable para Vías (Calles)
     */
    public function viasDataTable(Request $request)
    {
        try {
            $query = CatalogoCalle::select(['IDKEY', 'Nomvial', 'Tipovial', 'CLAVE']);

            return DataTables::eloquent($query)
                ->addColumn('acciones', function($via) {
                    return '
                        <button class="btn btn-sm btn-warning edit-via" title="Editar vía" data-via-id="'.$via->IDKEY.'">
                            <i class="bi bi-pen"></i>
                        </button>
                    ';
                })
                ->editColumn('Nomvial', function($via) {
                    return $via->Nomvial ?? 'N/A';
                })
                ->editColumn('Tipovial', function($via) {
                    return $via->Tipovial ?? 'N/A';
                })
                ->editColumn('CLAVE', function($via) {
                    return $via->CLAVE ?? 'N/A';
                })
                ->rawColumns(['acciones'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Error en viasDataTable:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error al procesar la solicitud de vías'
            ], 500);
        }
    }

    /**
     * Crear nueva zona
     */
    public function storeZona(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:53',
                'tipo' => 'required|string|max:21'
            ]);

            Colonias::create([
                'NOMBRE' => strtoupper(trim($validated['nombre'])),
                'TIPO' => strtoupper(trim($validated['tipo']))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Zona creada exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating zona:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear zona: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva vía
     */
    public function storeVia(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomvial' => 'required|string|max:100',
                'tipovial' => 'required|string|max:20',
                'clave' => 'nullable|string|max:20'
            ]);

            CatalogoCalle::create([
                'Nomvial' => strtoupper(trim($validated['nomvial'])),
                'Tipovial' => strtoupper(trim($validated['tipovial'])),
                'CLAVE' => $validated['clave'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vía creada exitosamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating via:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear vía: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener zona para edición
     */
    public function showZona($id)
    {
        try {
            $zona = Colonias::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $zona
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Zona no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar zona
     */
    public function updateZona(Request $request, $id)
    {
        try {
            $zona = Colonias::findOrFail($id);
            
            $validated = $request->validate([
                'nombre' => 'required|string|max:53',
                'tipo' => 'required|string|max:21'
            ]);

            $zona->update([
                'NOMBRE' => strtoupper(trim($validated['nombre'])),
                'TIPO' => strtoupper(trim($validated['tipo']))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Zona actualizada exitosamente',
                'data' => $zona
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating zona:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar zona: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener vía para edición
     */
    public function showVia($id)
    {
        try {
            $via = CatalogoCalle::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $via
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vía no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar vía
     */
    public function updateVia(Request $request, $id)
    {
        try {
            $via = CatalogoCalle::findOrFail($id);
            
            $validated = $request->validate([
                'nomvial' => 'required|string|max:100',
                'tipovial' => 'required|string|max:20',
                'clave' => 'nullable|string|max:20'
            ]);

            $via->update([
                'Nomvial' => strtoupper(trim($validated['nomvial'])),
                'Tipovial' => strtoupper(trim($validated['tipovial'])),
                'CLAVE' => $validated['clave'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vía actualizada exitosamente',
                'data' => $via
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating via:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar vía: ' . $e->getMessage()
            ], 500);
        }
    }
}