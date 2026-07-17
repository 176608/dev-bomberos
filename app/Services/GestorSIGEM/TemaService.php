<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\TemaV2;

class TemaService
{
    public function __construct(
        private TemaV2 $temaV2,
    ) {}

    public function listar(): array
    {
        $temas = $this->temaV2->orderBy('tema_titulo', 'asc')->get();
        $siguienteOrden = ($this->temaV2->max('orden_indice') ?? 0) + 1;

        return [
            'temas' => $temas,
            'siguienteOrden' => $siguienteOrden,
        ];
    }

    public function crear(array $datos): TemaV2
    {
        if (!isset($datos['orden_indice']) || $datos['orden_indice'] === null || $datos['orden_indice'] === '' || $datos['orden_indice'] === false) {
            $maxOrden = $this->temaV2->max('orden_indice') ?? 0;
            $datos['orden_indice'] = $maxOrden + 1;
        }

        if (isset($datos['icono'])) {
            $datos['icono'] = $this->sanitizarIcono($datos['icono']);
        }

        $datos['publicado'] = $datos['publicado'] ?? false;

        return $this->temaV2->crear($datos);
    }

    public function actualizar(int $id, array $datos): TemaV2
    {
        $tema = $this->temaV2->obtenerPorId($id);

        if (!$tema) {
            throw new \RuntimeException('Tema no encontrado');
        }

        if (isset($datos['icono'])) {
            $datos['icono'] = $this->sanitizarIcono($datos['icono']);
        }

        $datos['publicado'] = $datos['publicado'] ?? false;

        $tema->actualizar($datos);

        return $tema;
    }

    private function sanitizarIcono(string $icono): string
    {
        $trimmed = trim($icono);

        if (preg_match('/class=["\']([^"\']+)["\']/', $trimmed, $matches)) {
            return trim($matches[1]);
        }

        $limpio = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $trimmed);
        $limpio = preg_replace('/\s+/', ' ', $limpio);

        if (!str_starts_with($limpio, 'bi ')) {
            $limpio = 'bi ' . ltrim($limpio, 'bi-');
        }

        return $limpio;
    }

    public function eliminar(int $id): string
    {
        $tema = $this->temaV2->obtenerPorId($id);

        if (!$tema) {
            throw new \RuntimeException('Tema no encontrado');
        }

        $subtemasCount = $tema->subtemas()->count();

        if ($subtemasCount > 0) {
            throw new \RuntimeException(
                "No se puede eliminar el tema '{$tema->tema_titulo}' porque tiene {$subtemasCount} subtema(s) asociado(s)."
            );
        }

        $nombreTema = $tema->tema_titulo;
        $tema->eliminar();

        return $nombreTema;
    }

    public function siguienteOrden(): int
    {
        return ($this->temaV2->max('orden_indice') ?? 0) + 1;
    }
}
