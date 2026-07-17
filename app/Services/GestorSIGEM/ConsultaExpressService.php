<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;

class ConsultaExpressService
{
    public function __construct(
        private ce_tema $ceTema,
        private ce_subtema $ceSubtema,
        private ce_contenido $ceContenido,
    ) {}

    public function listarTemas(): array
    {
        $temas = $this->ceTema->obtenerTodos();
        $subtemas = $this->ceSubtema->obtenerTodos();
        $contenidos = $this->ceContenido->with(['subtema.tema'])->orderBy('created_at', 'desc')->get();

        return [
            'ce_temas' => $temas,
            'ce_subtemas' => $subtemas,
            'ce_contenidos' => $contenidos,
        ];
    }

    // ============ CRUD TEMAS CE ============

    public function crearTema(array $datos): ce_tema
    {
        return $this->ceTema->crear($datos);
    }

    public function actualizarTema(int $id, array $datos): ce_tema
    {
        $tema = $this->ceTema->obtenerPorId($id);

        if (!$tema) {
            throw new \RuntimeException('Tema CE no encontrado');
        }

        $tema->actualizar($datos);

        return $tema;
    }

    public function eliminarTema(int $id): string
    {
        $tema = $this->ceTema->obtenerPorId($id);

        if (!$tema) {
            throw new \RuntimeException('Tema CE no encontrado');
        }

        $subtemasCount = $tema->subtemas()->count();

        if ($subtemasCount > 0) {
            throw new \RuntimeException(
                "No se puede eliminar el tema CE '{$tema->tema}' porque tiene {$subtemasCount} subtema(s) asociado(s)."
            );
        }

        $nombre = $tema->tema;
        $tema->eliminar();

        return $nombre;
    }

    // ============ CRUD SUBTEMAS CE ============

    public function crearSubtema(array $datos): ce_subtema
    {
        return $this->ceSubtema->crear($datos);
    }

    public function actualizarSubtema(int $id, array $datos): ce_subtema
    {
        $subtema = $this->ceSubtema->obtenerPorId($id);

        if (!$subtema) {
            throw new \RuntimeException('Subtema CE no encontrado');
        }

        $subtema->actualizar($datos);

        return $subtema;
    }

    public function eliminarSubtema(int $id): array
    {
        $subtema = $this->ceSubtema->obtenerPorId($id);

        if (!$subtema) {
            throw new \RuntimeException('Subtema CE no encontrado');
        }

        $contenidosCount = $subtema->contenidos()->count();

        if ($contenidosCount > 0) {
            throw new \RuntimeException(
                "No se puede eliminar el subtema CE '{$subtema->ce_subtema}' porque tiene {$contenidosCount} contenido(s) asociado(s)."
            );
        }

        $nombreSubtema = $subtema->ce_subtema;
        $nombreTema = $subtema->tema ? $subtema->tema->tema : 'Sin tema';

        $subtema->eliminar();

        return [
            'subtema' => $nombreSubtema,
            'tema' => $nombreTema,
        ];
    }

    // ============ CRUD CONTENIDOS CE ============

    public function crearContenido(array $datos): ce_contenido
    {
        $filas = (int) $datos['tabla_filas'];
        $columnas = (int) $datos['tabla_columnas'];

        $this->ceContenido->validarTabla($filas, $columnas, $datos);

        $estructura_tabla = $this->ceContenido->crearEstructuraTabla($filas, $columnas, $datos);

        return $this->ceContenido->create([
            'ce_subtema_id' => $datos['ce_subtema_id'],
            'titulo_tabla' => $datos['titulo_tabla'],
            'pie_tabla' => $datos['pie_tabla'] ?? null,
            'tabla_filas' => $filas,
            'tabla_columnas' => $columnas,
            'tabla_datos' => $estructura_tabla,
        ]);
    }

    public function actualizarContenido(int $id, array $datos): ce_contenido
    {
        $contenido = $this->ceContenido->find($id);

        if (!$contenido) {
            throw new \RuntimeException('Contenido CE no encontrado');
        }

        $filas = (int) $datos['tabla_filas'];
        $columnas = (int) $datos['tabla_columnas'];

        $this->ceContenido->validarTabla($filas, $columnas, $datos);

        $estructura_tabla = $this->ceContenido->crearEstructuraTabla($filas, $columnas, $datos);

        $contenido->update([
            'ce_subtema_id' => $datos['ce_subtema_id'],
            'titulo_tabla' => $datos['titulo_tabla'],
            'pie_tabla' => $datos['pie_tabla'] ?? null,
            'tabla_filas' => $filas,
            'tabla_columnas' => $columnas,
            'tabla_datos' => $estructura_tabla,
        ]);

        return $contenido;
    }

    public function eliminarContenido(int $id): array
    {
        $contenido = $this->ceContenido->find($id);

        if (!$contenido) {
            throw new \RuntimeException('Contenido CE no encontrado');
        }

        $tituloTabla = $contenido->titulo_tabla ?: 'Tabla sin título';
        $nombreSubtema = $contenido->subtema ? $contenido->subtema->ce_subtema : 'Sin subtema';
        $dimensiones = "{$contenido->tabla_filas}x{$contenido->tabla_columnas}";

        $contenido->delete();

        return [
            'titulo' => $tituloTabla,
            'dimensiones' => $dimensiones,
            'subtema' => $nombreSubtema,
        ];
    }

    // ============ AJAX HELPERS ============

    public function obtenerSubtemasPorTema(int $tema_id): array
    {
        return $this->ceSubtema->where('ce_tema_id', $tema_id)
            ->orderBy('ce_subtema_id', 'asc')
            ->get(['ce_subtema_id', 'ce_subtema'])
            ->toArray();
    }

    public function obtenerContenidoParaVista(int $id): ?ce_contenido
    {
        return $this->ceContenido->with(['subtema.tema'])->find($id);
    }
}
