<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\SubtemaV2;
use App\Models\SIGEM\CuadroEstadistico;
use App\Services\SecureFileUpload;
use Illuminate\Http\UploadedFile;

class SubtemaService
{
    public function __construct(
        private SubtemaV2 $subtemaV2,
        private SecureFileUpload $fileUploader,
    ) {}

    public function listar(): array
    {
        $subtemas = $this->subtemaV2->with('tema')->orderBy('subtema_titulo', 'asc')->get();
        $temas = \App\Models\SIGEM\TemaV2::orderBy('tema_titulo', 'asc')->get();

        return [
            'subtemas' => $subtemas,
            'temas' => $temas,
        ];
    }

    public function crear(array $datos, ?UploadedFile $imagen = null): SubtemaV2
    {
        if (!isset($datos['orden_indice']) || !$datos['orden_indice']) {
            $datos['orden_indice'] = $this->subtemaV2->siguienteOrden($datos['tema_id']);
        }

        $datos['publicado'] = $datos['publicado'] ?? false;

        if ($imagen) {
            try {
                $datos['imagen'] = $this->fileUploader->uploadImage($imagen);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException('Error de seguridad en el archivo: ' . $e->getMessage());
            }
        }

        return $this->subtemaV2->crear($datos);
    }

    public function actualizar(int $id, array $datos, ?UploadedFile $imagen = null, bool $removeImagen = false): SubtemaV2
    {
        $subtema = $this->subtemaV2->obtenerPorId($id);

        if (!$subtema) {
            throw new \RuntimeException('Subtema no encontrado');
        }

        $datos['publicado'] = $datos['publicado'] ?? false;

        if (isset($datos['tema_id']) && $datos['tema_id'] != $subtema->tema_id) {
            $datos['orden_indice'] = $this->subtemaV2->siguienteOrden($datos['tema_id']);
        } elseif (!isset($datos['orden_indice']) || !$datos['orden_indice']) {
            $datos['orden_indice'] = $subtema->orden_indice;
        }

        if ($removeImagen) {
            if ($subtema->imagen) {
                $this->fileUploader->deleteFile($subtema->imagen, 'imagenes/subtemas_u');
            }
            $datos['imagen'] = null;
        } elseif ($imagen) {
            try {
                $datos['imagen'] = $this->fileUploader->uploadImage($imagen, $subtema->imagen);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException('Error de seguridad en el archivo: ' . $e->getMessage());
            }
        }

        $subtema->actualizar($datos);

        return $subtema;
    }

    public function eliminar(int $id): string
    {
        $subtema = $this->subtemaV2->obtenerPorId($id);

        if (!$subtema) {
            throw new \RuntimeException('Subtema no encontrado');
        }

        $cuadrosCount = CuadroEstadistico::where('subtema_id', $id)->count();

        if ($cuadrosCount > 0) {
            throw new \RuntimeException(
                "No se puede eliminar el subtema '{$subtema->subtema_titulo}' porque tiene {$cuadrosCount} cuadro(s) estadístico(s) asociado(s)."
            );
        }

        if ($subtema->imagen) {
            $this->fileUploader->deleteFile($subtema->imagen, 'imagenes/subtemas_u');
        }

        $nombreSubtema = $subtema->subtema_titulo;
        $nombreTema = $subtema->tema ? $subtema->tema->tema_titulo : 'Sin tema';

        $subtema->eliminar();

        return "{$nombreSubtema}' del tema '{$nombreTema}";
    }

    public function siguienteOrden(int $tema_id): int
    {
        return $this->subtemaV2->siguienteOrden($tema_id);
    }

    public function obtenerSubtemasPorTema(int $tema_id): array
    {
        return $this->subtemaV2->where('tema_id', $tema_id)
            ->orderBy('orden_indice', 'asc')
            ->orderBy('subtema_titulo', 'asc')
            ->get(['subtema_id', 'subtema_titulo'])
            ->toArray();
    }
}
