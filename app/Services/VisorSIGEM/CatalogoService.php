<?php

namespace App\Services\VisorSIGEM;

use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\Cuadro;
use Illuminate\Support\Collection;

class CatalogoService
{
    public function __construct(
        private TemaV2 $temaV2,
        private Cuadro $cuadro,
    ) {}

    public function obtenerCatalogo(?bool $esDesarrollador): array
    {
        $temasQuery = $this->temaV2->with(['subtemas' => function ($q) use ($esDesarrollador) {
            $q->orderBy('orden_indice');
            if (!$esDesarrollador) {
                $q->where('publicado', true);
            }
        }])->orderBy('orden_indice');

        if (!$esDesarrollador) {
            $temasQuery->where('publicado', true);
        }

        $temas = $temasQuery->get();

        $indicadoresQuery = $this->cuadro->query();
        if (!$esDesarrollador) {
            $indicadoresQuery->where('publicado', true);
        }
        $indicadores = $indicadoresQuery->get();

        $indicadores = $indicadores->sort(function ($a, $b) {
            $aParts = explode('.', $a->codigo_cuadro);
            $bParts = explode('.', $b->codigo_cuadro);
            $len = max(count($aParts), count($bParts));
            for ($i = 0; $i < $len; $i++) {
                $aVal = (int)($aParts[$i] ?? 0);
                $bVal = (int)($bParts[$i] ?? 0);
                if ($aVal !== $bVal) return $aVal <=> $bVal;
            }
            return 0;
        });

        return [
            'temas' => $temas,
            'indicadores' => $indicadores,
            'esDesarrollador' => $esDesarrollador,
        ];
    }

    public function cuadrosPorSubtema(int $subtema_id, ?bool $esDesarrollador): array
    {
        try {
            $query = $this->cuadro->where('subtema_id', $subtema_id)
                ->orderBy('codigo_cuadro');

            if (!$esDesarrollador) {
                $query->where('publicado', true);
            }

            $cuadros = $query->get()
                ->map(function ($cuadro) {
                    return [
                        'cuadro_id' => $cuadro->cuadro_id,
                        'codigo_cuadro' => $cuadro->codigo_cuadro,
                        'c_titulo' => $cuadro->c_titulo,
                        'c_subtitulo' => $cuadro->c_subtitulo,
                        'tipo_mapa_pdf' => $cuadro->tipo_mapa_pdf,
                        'permite_grafica' => $cuadro->permite_grafica,
                        'publicado' => $cuadro->publicado,
                    ];
                });

            return [
                'success' => true,
                'cuadros' => $cuadros,
                'total_cuadros' => $cuadros->count(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al cargar cuadros: ' . $e->getMessage(),
                'cuadros' => [],
            ];
        }
    }
}
