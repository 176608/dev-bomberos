<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\AuditoriaDataset;
use App\Models\SIGEM\CuadroDato;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuditoriaDatasetService
{
    private const SESION_KEY = 'audit_dataset_open_';
    private const SESION_USER_KEY = 'audit_dataset_user_';

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
            'datos_secciones' => $this->capturarDatosSecciones($cuadroId),
        ], now()->addHours(8));

        $this->agregarAlIndice($cuadroId);
    }

    public function cerrarSesion(int $cuadroId): void
    {
        if (!Auth::check()) return;

        $sesion = Cache::get(self::SESION_KEY . $cuadroId);
        if (!$sesion) return;

        $estadoActual = $this->obtenerEstadoSeguro($cuadroId);
        $estadoApertura = $sesion['estado_apertura'];

        Cache::forget(self::SESION_KEY . $cuadroId);
        $this->quitarDelIndice($cuadroId);

        if ($this->estadosIguales($estadoApertura, $estadoActual)) return;

        AuditoriaDataset::create([
            'user_id' => Auth::id(),
            'cuadro_id' => $cuadroId,
            'accion' => 'actualizar_dataset',
            'estado_anterior' => $estadoApertura,
            'estado_nuevo' => $estadoActual,
            'resumen_cambios' => $this->resumenCambios($estadoApertura, $estadoActual, $cuadroId, $sesion['datos_secciones'] ?? []),
        ]);
    }

    public function cerrarTodasSesiones(): void
    {
        if (!Auth::check()) return;

        $key = self::SESION_USER_KEY . Auth::id();
        $lista = Cache::get($key, []);

        foreach ($lista as $cuadroId) {
            $this->cerrarSesion((int) $cuadroId);
        }

        Cache::forget($key);
    }

    private function agregarAlIndice(int $cuadroId): void
    {
        $key = self::SESION_USER_KEY . Auth::id();
        $lista = Cache::get($key, []);

        if (!in_array($cuadroId, $lista)) {
            $lista[] = $cuadroId;
            Cache::put($key, $lista, now()->addHours(8));
        }
    }

    private function quitarDelIndice(int $cuadroId): void
    {
        $key = self::SESION_USER_KEY . Auth::id();
        $lista = Cache::get($key, []);
        $lista = array_values(array_diff($lista, [$cuadroId]));

        if (empty($lista)) {
            Cache::forget($key);
        } else {
            Cache::put($key, $lista, now()->addHours(8));
        }
    }

    private function capturarDatosSecciones(int $cuadroId): array
    {
        $mapa = [];
        $datos = CuadroDato::where('cuadro_id', $cuadroId)
            ->select('seccion_id', 'cat_vertical_id', 'cat_horizontal_id', 'valor')
            ->get();

        foreach ($datos as $d) {
            $mapa[$d->seccion_id][$d->cat_vertical_id . '|' . $d->cat_horizontal_id] = $d->valor;
        }
        return $mapa;
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
            'secciones' => $this->firmaSecciones($estado['secciones'] ?? []),
        ];

        if (array_key_exists('tipos_grafica_permitida', $estado)) {
            $firma['tipos_grafica_permitida'] = $estado['tipos_grafica_permitida'];
        }

        return json_encode($firma);
    }

    private function firmaSecciones(array $secciones): array
    {
        $ids = array_map(fn($s) => $s['seccion_id'] ?? null, $secciones);
        sort($ids);
        return $ids;
    }

    private function firmaCategorias(array $categorias): array
    {
        return array_map(fn($c) => [
            'id' => $c['categoria_id'] ?? null,
            'nombre' => $c['nombre'] ?? null,
            'orden' => $c['orden'] ?? null,
            'tipo' => $c['tipo'] ?? null,
            'padre' => $c['padre_id'] ?? null,
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

    private function resumenCambios(array $antes, array $despues, int $cuadroId, array $datosApertura): array
    {
        $aCeldas = $this->mapaCeldas($antes['data'] ?? []);
        $dCeldas = $this->mapaCeldas($despues['data'] ?? []);

        $celdasModificadas = 0;
        foreach ($dCeldas as $clave => $valor) {
            if (!array_key_exists($clave, $aCeldas) || $aCeldas[$clave] !== $valor) {
                $celdasModificadas++;
            }
        }

        $mapaCat = $this->mapaCategorias(
            $antes['all_verticales'] ?? [],
            $despues['all_verticales'] ?? [],
            $antes['all_horizontales'] ?? [],
            $despues['all_horizontales'] ?? [],
        );

        return [
            'dataset_creado' => empty($antes['tiene_dataset']) && !empty($despues['tiene_dataset']),
            'categorias_verticales' => $this->resumenCategorias($antes['verticales'] ?? [], $despues['verticales'] ?? [], $mapaCat),
            'categorias_horizontales' => $this->resumenCategorias($antes['horizontales'] ?? [], $despues['horizontales'] ?? [], $mapaCat),
            'secciones' => $this->resumenSecciones(
                $antes['secciones'] ?? [],
                $despues['secciones'] ?? [],
                $datosApertura,
                $this->capturarDatosSecciones($cuadroId),
            ),
            'celdas' => [
                'antes' => count($aCeldas),
                'despues' => count($dCeldas),
            ],
            'celdas_modificadas' => $celdasModificadas,
        ];
    }

    private function resumenSecciones(array $antes, array $despues, array $datosApertura, array $datosActuales): array
    {
        $idsA = array_column($antes, 'seccion_id');
        $idsB = array_column($despues, 'seccion_id');

        $agregadas = [];
        foreach ($despues as $s) {
            if (!in_array($s['seccion_id'], $idsA)) {
                $agregadas[] = [
                    'nombre' => $s['nombre'] ?? (string) $s['seccion_id'],
                    'datos' => $datosActuales[$s['seccion_id']] ?? [],
                ];
            }
        }

        $eliminadas = [];
        foreach ($antes as $s) {
            if (!in_array($s['seccion_id'], $idsB)) {
                $eliminadas[] = [
                    'nombre' => $s['nombre'] ?? (string) $s['seccion_id'],
                    'datos' => $datosApertura[$s['seccion_id']] ?? [],
                ];
            }
        }

        return [
            'antes' => count($idsA),
            'despues' => count($idsB),
            'agregadas' => $agregadas,
            'eliminadas' => $eliminadas,
        ];
    }

    private function mapaCategorias(array ...$listas): array
    {
        $mapa = [];
        foreach ($listas as $lista) {
            foreach ($lista as $c) {
                $mapa[$c['categoria_id']] = $c;
            }
        }
        return $mapa;
    }

    private function resumenCategorias(array $antes, array $despues, array $mapa): array
    {
        $idsA = array_column($antes, 'categoria_id');
        $idsB = array_column($despues, 'categoria_id');

        return [
            'antes' => count($idsA),
            'despues' => count($idsB),
            'agregadas' => $this->agruparPorPadre(array_diff($idsB, $idsA), $mapa),
            'eliminadas' => $this->agruparPorPadre(array_diff($idsA, $idsB), $mapa),
        ];
    }

    private function agruparPorPadre(array $ids, array $mapa): array
    {
        $grupos = [];
        foreach ($ids as $id) {
            $c = $mapa[$id] ?? null;
            $nombre = $c['nombre'] ?? (string) $id;

            if (empty($c['padre_id'])) {
                $grupos[] = ['padre' => null, 'nombre' => $nombre];
                continue;
            }

            $padre = $mapa[$c['padre_id']] ?? null;
            $nombrePadre = $padre['nombre'] ?? (string) $c['padre_id'];
            $key = 'p_' . $c['padre_id'];

            if (!isset($grupos[$key])) {
                $grupos[$key] = ['padre' => $nombrePadre, 'hijos' => []];
            }
            $grupos[$key]['hijos'][] = $nombre;
        }
        return array_values($grupos);
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
