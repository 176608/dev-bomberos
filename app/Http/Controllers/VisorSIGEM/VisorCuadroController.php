<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Services\GestorSIGEM\DatasetService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class VisorCuadroController extends Controller
{
    private const CACHE_TTL = 300;

    public function __construct(
        private DatasetService $datasetService,
    ) {}

    private function getUserRoleDisplay(): ?string
    {
        if (!auth()->check()) return null;
        if (auth()->user()->hasRole('Desarrollador')) return 'Desarrollador';
        if (auth()->user()->hasRole('Estadistico')) return 'Estadístico';
        return null;
    }

    protected function verificarAccesoCuadro(Cuadro $cuadro): ?array
    {
        if ($cuadro->publicado) return null;
        if (!$this->tieneCredenciales()) {
            abort(404);
        }
        try {
            $this->datasetService->obtenerEstado($cuadro->cuadro_id);
            return null;
        } catch (\RuntimeException) {
            return ['error' => 'El cuadro seleccionado no tiene un dataset asociado.'];
        }
    }

    private function responderError(string $mensaje)
    {
        if (request()->expectsJson()) {
            return response()->json(['error' => $mensaje], 404);
        }
        abort(404, $mensaje);
    }

    public function dataset(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro) abort(404);

        $error = $this->verificarAccesoCuadro($cuadro);
        if ($error) return $this->responderError($error['error']);

        if ($cuadro->tipo_mapa_pdf) {
            return redirect()->route('sigem.v2.cuadro.mapa', $id);
        }

        $estadoInicial = $this->cachedEstado($id);

        $this->registrarMetrica($cuadro, 'dataset');

        return view('VisorSIGEM.cuadro.dataset', [
            'cuadro' => $cuadro,
            'estadoInicial' => $estadoInicial,
            'esDesarrollador' => $this->esDesarrollador(),
            'userRoleDisplay' => $this->getUserRoleDisplay(),
        ]);
    }

    public function mapa(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro) abort(404);

        $error = $this->verificarAccesoCuadro($cuadro);
        if ($error) return $this->responderError($error['error']);

        if (!$cuadro->tipo_mapa_pdf) {
            return redirect()->route('sigem.v2.cuadro.dataset', $id);
        }

        $this->registrarMetrica($cuadro, 'mapa');

        return view('VisorSIGEM.cuadro.mapa', [
            'cuadro' => $cuadro,
            'esDesarrollador' => $this->esDesarrollador(),
            'userRoleDisplay' => $this->getUserRoleDisplay(),
        ]);
    }

    public function descargarMapa(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro) abort(404);

        $error = $this->verificarAccesoCuadro($cuadro);
        if ($error) return $this->responderError($error['error']);

        if (!$cuadro->tipo_mapa_pdf || !$cuadro->pdf_file) {
            abort(404);
        }

        $filename = $cuadro->pdf_file;
        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(404);
        }

        $disk = Storage::disk('mapas');
        if (!$disk->exists($filename)) {
            abort(404);
        }

        $this->registrarMetrica($cuadro, 'mapa_pdf');

        $fecha = now()->format('d_m_Y');
        $nombre = $cuadro->codigo_cuadro . '_' . $fecha . '.pdf';

        return $disk->download($filename, $nombre);
    }

    public function verMapa(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro) abort(404);

        $error = $this->verificarAccesoCuadro($cuadro);
        if ($error) return $this->responderError($error['error']);

        if (!$cuadro->tipo_mapa_pdf || !$cuadro->pdf_file) {
            abort(404);
        }

        $filename = $cuadro->pdf_file;
        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(404);
        }

        $disk = Storage::disk('mapas');
        if (!$disk->exists($filename)) {
            abort(404);
        }

        $this->registrarMetrica($cuadro, 'mapa_pdf');

        return $disk->response($filename);
    }

    public function grafica(int $id)
    {
        $cuadro = Cuadro::obtenerPorId($id);
        if (!$cuadro) abort(404);

        $error = $this->verificarAccesoCuadro($cuadro);
        if ($error) return $this->responderError($error['error']);

        $estadoInicial = $this->cachedEstado($id);

        $this->registrarMetrica($cuadro, 'grafica');

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
        if (!$cuadro) {
            return response()->json(['error' => 'Cuadro no encontrado'], 404);
        }

        $error = $this->verificarAccesoCuadro($cuadro);
        if ($error) return response()->json($error, 404);

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
