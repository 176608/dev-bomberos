<?php

namespace App\Http\Controllers\VisorDictamenes;

use App\Models\GestorDictamenes\Dictamen;
use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DictamenController extends Controller
{
    public function publicIndex(Request $request)
    {
        $query = Dictamen::query();

        if ($request->filled('estatus') && in_array($request->estatus, Dictamen::FILTERABLE_STATUSES, true)) {
            $query->where('estatus', $request->estatus);
        } else {
            $query->where('estatus', '!=', Dictamen::DESHABILITADO);
        }

        if ($request->filled('revisado_por')) {
            $query->where('revisado_por', $request->revisado_por);
        }

        if ($request->filled('dependencia')) {
            $query->where('dependencia_empres', $request->dependencia);
        }

        if ($request->filled('nombre_puesto')) {
            $query->where('nombre_puesto', $request->nombre_puesto);
        }

        $dictamenes = $query->orderByDesc('fecha')->get();

        $this->anotarEstadoArchivo($dictamenes, $this->archivosDiscoPorAnio());

        $total = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)->count();
        $enviados = Dictamen::where('estatus', 'ENVIADO')->count();

        $revisadosPor = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('revisado_por')->where('revisado_por', '!=', '')
            ->distinct()->orderBy('revisado_por')->pluck('revisado_por');

        $dependencias = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('dependencia_empres')->where('dependencia_empres', '!=', '')
            ->distinct()->orderBy('dependencia_empres')->pluck('dependencia_empres');

        $nombresPuestos = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('nombre_puesto')->where('nombre_puesto', '!=', '')
            ->distinct()->orderBy('nombre_puesto')->pluck('nombre_puesto');

        $meses = [];
        $solicitudes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fechaInicio = now()->subMonths($i)->startOfMonth();
            $fechaFin = now()->subMonths($i)->endOfMonth();
            $meses[] = $fechaInicio->format('M');
            $solicitudes[] = Dictamen::whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('estatus', '!=', Dictamen::DESHABILITADO)
                ->count();
        }

        return view('visor-dictamenes.public', compact(
            'dictamenes',
            'total',
            'enviados',
            'revisadosPor',
            'dependencias',
            'nombresPuestos',
            'meses',
            'solicitudes'
        ));
    }

    private function archivosDiscoPorAnio(): array
    {
        $disco = Storage::disk('dictamenes');
        $porAnio = [];

        foreach ($disco->directories() as $dir) {
            if (preg_match('/^\d{4}$/', basename($dir))) {
                $porAnio[basename($dir)] = array_map('basename', $disco->files($dir));
            }
        }

        $porAnio[''] = array_map('basename', $disco->files());

        return $porAnio;
    }

    private function normalizarClave(?string $clave): string
    {
        return strtoupper(str_replace([' ', '-', '_'], '', trim((string) $clave)));
    }

    private function anotarEstadoArchivo($dictamenes, array $archivosPorAnio): void
    {
        foreach ($dictamenes as $d) {
            $clave = trim((string) $d->archivo_raw);
            $d->estado_archivo = 'sin_clave';
            $d->archivos_encontrados = [];

            if ($clave === '' || strtoupper($clave) === 'S/N' || strtoupper($clave) === 'S/D' || $d->anio === null) {
                continue;
            }

            $claveNorm = $this->normalizarClave($clave);
            if ($claveNorm === '') {
                continue;
            }

            $coincidencias = [];
            foreach ([$d->anio, ''] as $anio) {
                if (!isset($archivosPorAnio[$anio])) {
                    continue;
                }
                foreach ($archivosPorAnio[$anio] as $nombre) {
                    if ($this->normalizarClave($nombre) === $claveNorm
                        || preg_match('/^' . preg_quote($claveNorm, '/') . '/i', $this->normalizarClave($nombre))) {
                        $coincidencias[] = $anio !== '' ? $anio . '/' . $nombre : $nombre;
                    }
                }
            }

            $coincidencias = array_values(array_unique($coincidencias));
            $d->archivos_encontrados = $coincidencias;
            $n = count($coincidencias);
            $d->estado_archivo = $n === 0 ? 'no_encontrado' : ($n === 1 ? 'encontrado' : 'multiples');
        }
    }
}
