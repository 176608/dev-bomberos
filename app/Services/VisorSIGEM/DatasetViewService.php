<?php

namespace App\Services\VisorSIGEM;

use App\Models\SIGEM\Cuadro;

class DatasetViewService
{
    public function catalogo(?bool $esDesarrollador): array
    {
        $query = Cuadro::with(['subtema.tema']);
        if (!$esDesarrollador) {
            $query->where('publicado', true);
        }
        return $query->orderBy('codigo_cuadro')->get()->toArray();
    }

    public function datosCuadro(int $id, ?bool $esDesarrollador): ?array
    {
        $cuadro = Cuadro::obtenerPorId($id);

        if (!$cuadro) return null;
        if (!$esDesarrollador && !$cuadro->publicado) return null;

        $verticales = $cuadro->categoriasVerticales->sortBy('orden')->values();
        $horizontales = $cuadro->categoriasHorizontales->sortBy('orden')->values();

        $grid = [];
        $grid[] = array_merge(
            [''],
            $horizontales->pluck('nombre')->toArray()
        );

        $maxFila = $cuadro->datos->max('fila') ?? 0;
        $maxCol = $cuadro->datos->max('columna') ?? 0;

        $mapa = [];
        foreach ($cuadro->datos as $dato) {
            $mapa[$dato->fila][$dato->columna] = $dato->valor;
        }

        for ($f = 1; $f <= $maxFila; $f++) {
            $fila = [$verticales->get($f - 1)?->nombre ?? "Fila $f"];
            for ($c = 1; $c <= $maxCol; $c++) {
                $fila[] = $mapa[$f][$c] ?? '';
            }
            $grid[] = $fila;
        }

        return [
            'cuadro' => $cuadro->toArray(),
            'verticales' => $verticales->toArray(),
            'horizontales' => $horizontales->toArray(),
            'tabla' => $grid,
            'max_filas' => $maxFila,
            'max_columnas' => $maxCol,
        ];
    }
}
