<?php

namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Requests\GestorSIGEM\StoreTemaCERequest;
use App\Http\Requests\GestorSIGEM\StoreContenidoCERequest;
use App\Services\GestorSIGEM\ConsultaExpressService;

class ConsultaExpressController extends Controller
{
    public function __construct(
        private ConsultaExpressService $ceService,
    ) {}

    public function index()
    {
        $data = $this->ceService->listarTemas();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.CRUD_consultas',
            'ce_temas' => $data['ce_temas'],
            'ce_contenidos' => $data['ce_contenidos'],
        ]);
    }

    public function storeTema(StoreTemaCERequest $request)
    {
        try {
            $temaCE = $this->ceService->crearTema($request->validated());

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Tema CE '{$temaCE->tema}' creado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al crear el tema CE: ' . $e->getMessage());
        }
    }

    public function updateTema(StoreTemaCERequest $request, $id)
    {
        try {
            $temaCE = $this->ceService->actualizarTema((int) $id, $request->validated());

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Tema CE actualizado a '{$temaCE->tema}' exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al actualizar el tema CE: ' . $e->getMessage());
        }
    }

    public function destroyTema($id)
    {
        try {
            $nombre = $this->ceService->eliminarTema((int) $id);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Tema CE '{$nombre}' eliminado exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al eliminar el tema CE: ' . $e->getMessage());
        }
    }

    public function storeContenido(StoreContenidoCERequest $request)
    {
        try {
            $contenidoCE = $this->ceService->crearContenido($request->validated());

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Contenido CE '{$contenidoCE->titulo_tabla}' creado exitosamente con tabla de {$contenidoCE->tabla_filas}x{$contenidoCE->tabla_columnas}");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al crear el contenido CE: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateContenido(StoreContenidoCERequest $request, $id)
    {
        try {
            $this->ceService->actualizarContenido((int) $id, $request->validated());

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Contenido CE actualizado exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al actualizar el contenido CE: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyContenido($id)
    {
        try {
            $info = $this->ceService->eliminarContenido((int) $id);

            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('success', "Contenido CE '{$info['titulo']}' ({$info['dimensiones']}) del subtema '{$info['subtema']}' eliminado exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.consultas')
                ->with('error', 'Error al eliminar el contenido CE: ' . $e->getMessage());
        }
    }

    public function contenido($id)
    {
        try {
            $contenido = $this->ceService->obtenerContenidoParaVista((int) $id);

            if (!$contenido) {
                return response()->json(['error' => 'Contenido no encontrado'], 404);
            }

            $tablaHtml = $contenido->renderizarTabla();

            return response()->json([
                'success' => true,
                'contenido' => [
                    'ce_contenido_id' => $contenido->ce_contenido_id,
                    'titulo_tabla' => $contenido->titulo_tabla,
                    'pie_tabla' => $contenido->pie_tabla,
                    'tabla_filas' => $contenido->tabla_filas,
                    'tabla_columnas' => $contenido->tabla_columnas,
                    'tabla_datos' => $contenido->tabla_datos,
                    'ce_subtema_id' => $contenido->ce_subtema_id,
                    'created_at' => $contenido->created_at,
                    'subtema' => $contenido->subtema ? [
                        'ce_subtema_id' => $contenido->subtema->ce_subtema_id,
                        'ce_subtema' => $contenido->subtema->ce_subtema,
                        'tema' => $contenido->subtema->tema ? [
                            'ce_tema_id' => $contenido->subtema->tema->ce_tema_id,
                            'tema' => $contenido->subtema->tema->tema,
                        ] : null,
                    ] : null,
                ],
                'tabla_html' => $tablaHtml,
                'resumen' => $contenido->resumen_tabla ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno del servidor: ' . $e->getMessage(),
            ], 500);
        }
    }
}
