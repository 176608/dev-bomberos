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
        $contenidos = $this->ceContenido->with(['subtema.tema'])->orderBy('created_at', 'desc')->get();

        return [
            'ce_temas' => $temas,
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


    // ============ CRUD CONTENIDOS CE (incluye subtema) ============

    public function crearContenido(array $datos): ce_contenido
    {
        $filas = (int) $datos['tabla_filas'];
        $columnas = (int) $datos['tabla_columnas'];

        $this->ceContenido->validarTabla($filas, $columnas, $datos);

        $estructura_tabla = $this->ceContenido->crearEstructuraTabla($filas, $columnas, $datos);

        $subtema = $this->ceSubtema->crear([
            'ce_tema_id' => $datos['ce_tema_id'],
            'ce_subtema' => $datos['ce_subtema_nombre'],
        ]);

        return $this->ceContenido->create([
            'ce_subtema_id' => $subtema->ce_subtema_id,
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

        if ($contenido->subtema) {
            $contenido->subtema->actualizar([
                'ce_tema_id' => $datos['ce_tema_id'],
                'ce_subtema' => $datos['ce_subtema_nombre'],
            ]);
        }

        $contenido->update([
            'ce_subtema_id' => $contenido->ce_subtema_id,
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

        $subtemaId = $contenido->ce_subtema_id;
        $contenido->delete();

        if ($subtemaId) {
            $this->ceSubtema->obtenerPorId($subtemaId)?->eliminar();
        }

        return [
            'titulo' => $tituloTabla,
            'dimensiones' => $dimensiones,
            'subtema' => $nombreSubtema,
        ];
    }

    // ============ AJAX HELPERS ============

    public function obtenerContenidoParaVista(int $id): ?ce_contenido
    {
        return $this->ceContenido->with(['subtema.tema'])->find($id);
    }
}
