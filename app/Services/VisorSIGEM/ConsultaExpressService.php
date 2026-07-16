<?php

namespace App\Services\VisorSIGEM;

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

    public function obtenerTemas()
    {
        return $this->ceTema->with('subtemas')->orderBy('ce_tema_id')->get();
    }

    public function subtemasPorTema(int $tema_id): array
    {
        try {
            $subtemas = $this->ceSubtema->where('ce_tema_id', $tema_id)
                ->orderBy('ce_subtema')
                ->get();

            return [
                'success' => true,
                'subtemas' => $subtemas,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al cargar subtemas: ' . $e->getMessage(),
            ];
        }
    }

    public function obtenerContenido(int $subtema_id): array
    {
        try {
            $contenido = $this->ceContenido->where('ce_subtema_id', $subtema_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$contenido) {
                return [
                    'success' => false,
                    'message' => 'No se encontró contenido para este subtema',
                ];
            }

            $subtema = $this->ceSubtema->with('tema')->find($subtema_id);

            return [
                'success' => true,
                'message' => 'Contenido cargado exitosamente',
                'contenido' => [
                    'ce_contenido_id' => $contenido->ce_contenido_id,
                    'titulo_tabla' => $contenido->titulo_tabla,
                    'pie_tabla' => $contenido->pie_tabla,
                    'tabla_filas' => $contenido->tabla_filas,
                    'tabla_columnas' => $contenido->tabla_columnas,
                    'tabla_datos' => $contenido->tabla_datos,
                    'created_at' => $contenido->created_at,
                    'updated_at' => $contenido->updated_at,
                ],
                'subtema' => $subtema ? [
                    'ce_subtema_id' => $subtema->ce_subtema_id,
                    'ce_subtema' => $subtema->ce_subtema,
                    'tema' => $subtema->tema ? [
                        'ce_tema_id' => $subtema->tema->ce_tema_id,
                        'tema' => $subtema->tema->tema,
                    ] : null,
                ] : null,
                'actualizado' => $contenido->updated_at ? $contenido->updated_at->format('d/m/Y H:i:s') : null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al cargar contenido: ' . $e->getMessage(),
            ];
        }
    }
}
