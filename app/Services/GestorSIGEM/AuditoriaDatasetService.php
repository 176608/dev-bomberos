<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\AuditoriaDataset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuditoriaDatasetService
{
    private const SESION_KEY = 'audit_dataset_open_';

    public function __construct(
        private DatasetService $datasetService,
    ) {}

    public function abrirSesion(int $cuadroId): void
    {
        if (!Auth::check()) return;

        $this->cerrarSesion($cuadroId);

        $estado = $this->obtenerEstadoSeguro($cuadroId);

        $sinHistorial = AuditoriaDataset::where('cuadro_id', $cuadroId)->doesntExist();

        if ($sinHistorial && empty($estado['tiene_dataset'])) {
            AuditoriaDataset::create([
                'user_id' => Auth::id(),
                'cuadro_id' => $cuadroId,
                'accion' => 'crear_dataset',
                'estado_anterior' => null,
                'estado_nuevo' => $estado,
                'resumen_cambios' => ['dataset_creado' => false],
            ]);
        }

        Cache::put(self::SESION_KEY . $cuadroId, [
            'user_id' => Auth::id(),
            'estado_apertura' => $estado,
        ], now()->addHours(8));
    }

    public function cerrarSesion(int $cuadroId): void
    {
        if (!Auth::check()) return;

        $sesion = Cache::get(self::SESION_KEY . $cuadroId);
        if (!$sesion) return;

        $estadoActual = $this->obtenerEstadoSeguro($cuadroId);
        $estadoApertura = $sesion['estado_apertura'];

        Cache::forget(self::SESION_KEY . $cuadroId);

        if ($this->estadosIguales($estadoApertura, $estadoActual)) return;

        AuditoriaDataset::create([
            'user_id' => Auth::id(),
            'cuadro_id' => $cuadroId,
            'accion' => 'actualizar_dataset',
            'estado_anterior' => $estadoApertura,
            'estado_nuevo' => $estadoActual,
            'resumen_cambios' => $this->resumenCambios($estadoApertura, $estadoActual),
        ]);
    }

    private function obtenerEstadoSeguro(int $cuadroId): array
    {
        try {
            return $this->datasetService->obtenerEstado($cuadroId);
        } catch (\RuntimeException) {
            return [
                'tiene_dataset' => false,
                'verticales' => [],
                'horizontales' => [],
                'all_verticales' => [],
                'all_horizontales' => [],
                'headers' => [],
                'labels' => [],
                'data' => [],
                'secciones' => [],
                'seccion_activa_id' => null,
            ];
        }
    }

    private function estadosIguales(array $a, array $b): bool
    {
        return $this->firmaEstado($a) === $this->firmaEstado($b);
    }

    private function firmaEstado(array $estado): string
    {
        $firma = [
            'tiene_dataset' => !empty($estado['tiene_dataset']),
            'verticales' => $this->firmaCategorias($estado['all_verticales'] ?? []),
            'horizontales' => $this->firmaCategorias($estado['all_horizontales'] ?? []),
            'celdas' => $this->firmaCeldas($estado['data'] ?? []),
        ];

        if (array_key_exists('tipos_grafica_permitida', $estado)) {
            $firma['tipos_grafica_permitida'] = $estado['tipos_grafica_permitida'];
        }

        return json_encode($firma);
    }

    private function firmaCategorias(array $categorias): array
    {
        return array_map(fn($c) => [
            'id' => $c['categoria_id'] ?? null,
            'nombre' => $c['nombre'] ?? null,
            'orden' => $c['orden'] ?? null,
            'tipo' => $c['tipo'] ?? null,
            'padre' => $c['categoria_padre_id'] ?? null,
        ], $categorias);
    }

    private function firmaCeldas(array $data): array
    {
        $celdas = [];
        foreach ($data as $fila) {
            foreach ($fila as $celda) {
                $celdas[] = [
                    $celda['cat_vertical_id'] ?? null,
                    $celda['cat_horizontal_id'] ?? null,
                    $celda['valor'] ?? '',
                ];
            }
        }
        return $celdas;
    }

    private function resumenCambios(array $antes, array $despues): array
    {
        $aCeldas = $this->mapaCeldas($antes['data'] ?? []);
        $dCeldas = $this->mapaCeldas($despues['data'] ?? []);

        $celdasModificadas = 0;
        foreach ($dCeldas as $clave => $valor) {
            if (!array_key_exists($clave, $aCeldas) || $aCeldas[$clave] !== $valor) {
                $celdasModificadas++;
            }
        }

        return [
            'dataset_creado' => empty($antes['tiene_dataset']) && !empty($despues['tiene_dataset']),
            'categorias_verticales' => [
                'antes' => count($antes['all_verticales'] ?? []),
                'despues' => count($despues['all_verticales'] ?? []),
            ],
            'categorias_horizontales' => [
                'antes' => count($antes['all_horizontales'] ?? []),
                'despues' => count($despues['all_horizontales'] ?? []),
            ],
            'celdas' => [
                'antes' => count($aCeldas),
                'despues' => count($dCeldas),
            ],
            'celdas_modificadas' => $celdasModificadas,
        ];
    }

    private function mapaCeldas(array $data): array
    {
        $mapa = [];
        foreach ($data as $fila) {
            foreach ($fila as $celda) {
                $mapa[($celda['cat_vertical_id'] ?? '') . '|' . ($celda['cat_horizontal_id'] ?? '')] = $celda['valor'] ?? '';
            }
        }
        return $mapa;
    }
}
