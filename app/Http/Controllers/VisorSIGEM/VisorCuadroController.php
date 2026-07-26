<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Services\GestorSIGEM\DatasetService;
use Illuminate\Support\Facades\Cache;

class VisorCuadroController extends Controller
{
    private const CACHE_TTL = 300;

    public function __construct(
        private DatasetService $datasetService,
    ) {}

    private function esDesarrollador(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('Desarrollador') || auth()->user()->hasRole('Estadistico'));
    }

    private function getUserRoleDisplay(): ?string
    {
        if (!auth()->check()) return null;
        if (auth()->user()->hasRole('Desarrollador')) return 'Desarrollador';
        if (auth()->user()->hasRole('Estadistico')) return 'Estadístico';
        return null;
    }

    public function dataset(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro || !$cuadro->publicado) {
            abort(404);
        }

        $estadoInicial = $this->cachedEstado($id);

        return view('VisorSIGEM.cuadro.dataset', [
            'cuadro' => $cuadro,
            'estadoInicial' => $estadoInicial,
            'esDesarrollador' => $this->esDesarrollador(),
            'userRoleDisplay' => $this->getUserRoleDisplay(),
        ]);
    }

    public function grafica(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro || !$cuadro->publicado) {
            abort(404);
        }

        $estadoInicial = $this->cachedEstado($id);

        return view('VisorSIGEM.cuadro.grafica', [
            'cuadro' => $cuadro,
            'estadoInicial' => $estadoInicial,
            'esDesarrollador' => $this->esDesarrollador(),
            'userRoleDisplay' => $this->getUserRoleDisplay(),
        ]);
    }

    public function seccionData(int $id, int $seccion)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro || !$cuadro->publicado) {
            return response()->json(['error' => 'Cuadro no encontrado'], 404);
        }

        $cacheKey = "visor_cuadro_{$id}_seccion_{$seccion}";
        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id, $seccion) {
            $estado = $this->datasetService->obtenerEstado($id, $seccion);
            return $estado['data'] ?? [];
        });

        return response()->json(['data' => $data]);
    }

    private function cachedEstado(int $id): array
    {
        $cacheKey = "visor_cuadro_estado_{$id}";
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return $this->datasetService->obtenerEstado($id);
        });
    }
}
