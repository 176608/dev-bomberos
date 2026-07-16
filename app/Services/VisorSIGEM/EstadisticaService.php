<?php

namespace App\Services\VisorSIGEM;

use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\Cuadro;

class EstadisticaService
{
    public function __construct(
        private TemaV2 $temaV2,
        private Cuadro $cuadro,
    ) {}

    public function obtenerTemas(?bool $esDesarrollador): array
    {
        $query = $this->temaV2->withCount('subtemas')
            ->orderBy('orden_indice');

        if (!$esDesarrollador) {
            $query->where('publicado', true);
        }

        $temas = $query->get();

        return [
            'temas' => $temas,
            'esDesarrollador' => $esDesarrollador,
        ];
    }

    public function obtenerDatosTema(int $tema_id, ?bool $esDesarrollador): array
    {
        $tema = $this->temaV2->with(['subtemas' => function ($q) use ($esDesarrollador) {
            $q->orderBy('orden_indice');
            if (!$esDesarrollador) {
                $q->where('publicado', true);
            }
        }])->findOrFail($tema_id);

        if (!$esDesarrollador && !$tema->publicado) {
            abort(404);
        }

        $tema_subtemas = $tema->subtemas;

        $temasQuery = $this->temaV2->orderBy('orden_indice');
        if (!$esDesarrollador) {
            $temasQuery->where('publicado', true);
        }
        $temas = $temasQuery->get();

        $indicadores = collect([]);
        $subtema_seleccionado = null;

        if ($tema_subtemas && $tema_subtemas->count() > 0) {
            $subtema_seleccionado = $tema_subtemas->first();
            $query = $this->cuadro->where('subtema_id', $subtema_seleccionado->subtema_id)
                ->orderBy('codigo_cuadro');
            if (!$esDesarrollador) {
                $query->where('publicado', true);
            }
            $indicadores = $query->get();
        }

        return [
            'tema' => $tema,
            'temas' => $temas,
            'tema_subtemas' => $tema_subtemas,
            'subtema_seleccionado' => $subtema_seleccionado,
            'indicadores' => $indicadores,
            'esDesarrollador' => $esDesarrollador,
        ];
    }
}
