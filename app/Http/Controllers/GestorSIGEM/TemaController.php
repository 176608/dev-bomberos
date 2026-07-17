<?php

namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Requests\GestorSIGEM\StoreTemaV2Request;
use App\Http\Requests\GestorSIGEM\StoreSubtemaV2Request;
use App\Services\GestorSIGEM\TemaService;
use App\Services\GestorSIGEM\SubtemaService;

class TemaController extends Controller
{
    public function __construct(
        private TemaService $temaService,
        private SubtemaService $subtemaService,
    ) {}

    public function temas()
    {
        $data = $this->temaService->listar();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.CRUD_tema',
            'temas' => $data['temas'],
            'siguienteOrden' => $data['siguienteOrden'],
        ]);
    }

    public function storeTema(StoreTemaV2Request $request)
    {
        try {
            $tema = $this->temaService->crear($request->validated());

            return redirect()
                ->route('sgiem.admin.temas')
                ->with('success', "Tema '{$tema->tema_titulo}' creado exitosamente");

        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', 'Error al crear el tema: ' . $e->getMessage());
        }
    }

    public function updateTema(StoreTemaV2Request $request, $id)
    {
        try {
            $tema = $this->temaService->actualizar((int) $id, $request->validated());

            return redirect()
                ->route('sgiem.admin.temas')
                ->with('success', "Tema '{$tema->tema_titulo}' actualizado exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', 'Error al actualizar el tema: ' . $e->getMessage());
        }
    }

    public function destroyTema($id)
    {
        try {
            $nombreTema = $this->temaService->eliminar((int) $id);

            return redirect()
                ->route('sgiem.admin.temas')
                ->with('success', "Tema '{$nombreTema}' eliminado exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.temas')
                ->with('error', 'Error al eliminar el tema: ' . $e->getMessage());
        }
    }

    public function subtemas()
    {
        $data = $this->subtemaService->listar();
        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.CRUD_subtema',
            'subtemas' => $data['subtemas'],
            'temas' => $data['temas'],
        ]);
    }

    public function storeSubtema(StoreSubtemaV2Request $request)
    {
        try {
            $subtema = $this->subtemaService->crear(
                $request->validated(),
                $request->file('imagen')
            );

            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('success', "Subtema '{$subtema->subtema_titulo}' creado exitosamente en orden {$subtema->orden_indice}");

        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error de seguridad en el archivo: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error al crear el subtema: ' . $e->getMessage());
        }
    }

    public function updateSubtema(StoreSubtemaV2Request $request, $id)
    {
        try {
            $this->subtemaService->actualizar(
                (int) $id,
                $request->validated(),
                $request->file('imagen'),
                $request->boolean('remove_imagen')
            );

            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('success', "Subtema actualizado exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error de seguridad en el archivo: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error al actualizar el subtema: ' . $e->getMessage());
        }
    }

    public function destroySubtema($id)
    {
        try {
            $info = $this->subtemaService->eliminar((int) $id);

            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('success', "Subtema '{$info}' eliminado exitosamente");

        } catch (\RuntimeException $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('sgiem.admin.subtemas')
                ->with('error', 'Error al eliminar el subtema: ' . $e->getMessage());
        }
    }

    public function siguienteOrden($tema_id)
    {
        try {
            $orden = $this->subtemaService->siguienteOrden((int) $tema_id);
            return response()->json(['siguiente_orden' => $orden]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener el orden'], 500);
        }
    }

    public function subtemasPorTema($tema_id)
    {
        try {
            $subtemas = $this->subtemaService->obtenerSubtemasPorTema((int) $tema_id);
            return response()->json(['subtemas' => $subtemas]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener subtemas'], 500);
        }
    }
}
