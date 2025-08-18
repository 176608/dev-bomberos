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
            $query = Colonias::select(['IDKEY', 'NOMBRE', 'TIPO', 'ID_COLO']);

            return DataTables::eloquent($query)
                ->addColumn('acciones', function($zona) {
                    return '
                        <button class="btn btn-sm btn-primary view-zona" title="Ver información de la zona" data-zona-id="'.$zona->IDKEY.'">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning edit-zona" title="Editar zona" data-zona-id="'.$zona->IDKEY.'">
                            <i class="bi bi-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-zona" title="Eliminar zona" data-zona-id="'.$zona->IDKEY.'">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->editColumn('NOMBRE', function($zona) {
                    return $zona->NOMBRE ?? 'N/A';
                })
                ->editColumn('TIPO', function($zona) {
                    return $zona->TIPO ?? 'N/A';
                })
                ->editColumn('ID_COLO', function($zona) {
                    return $zona->ID_COLO ?? 'N/A';
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
                        <button class="btn btn-sm btn-primary view-via" title="Ver información de la vía" data-via-id="'.$via->IDKEY.'">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning edit-via" title="Editar vía" data-via-id="'.$via->IDKEY.'">
                            <i class="bi bi-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-via" title="Eliminar vía" data-via-id="'.$via->IDKEY.'">
                            <i class="bi bi-trash"></i>
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
                'tipo' => 'required|string|max:21',
                'id_colo' => 'nullable|integer'
            ]);

            Colonias::create([
                'NOMBRE' => strtoupper(trim($validated['nombre'])),
                'TIPO' => strtoupper(trim($validated['tipo'])),
                'ID_COLO' => $validated['id_colo'] ?? null
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
}