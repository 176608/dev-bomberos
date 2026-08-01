<?php

namespace App\Http\Controllers\VisorDictamenes;

use App\Models\GestorDictamenes\Dictamen;
use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DictamenController extends Controller
{
    private const MESES_ESP = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    public function publicIndex(Request $request)
    {
        $query = Dictamen::where('estatus', 'ENVIADO');

        if ($request->filled('anio') && preg_match('/^\d{4}$/', $request->anio)) {
            $query->where('anio', $request->anio);
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

        $total = Dictamen::where('estatus', 'ENVIADO')->count();
        $enviados = $total;

        $anios = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('anio')
            ->distinct()
            ->orderBy('anio')
            ->pluck('anio');

        $revisadosPor = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('revisado_por')->where('revisado_por', '!=', '')
            ->distinct()->orderBy('revisado_por')->pluck('revisado_por');

        $dependencias = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('dependencia_empres')->where('dependencia_empres', '!=', '')
            ->distinct()->orderBy('dependencia_empres')->pluck('dependencia_empres');

        $nombresPuestos = Dictamen::where('estatus', 'ENVIADO')
            ->whereNotNull('nombre_puesto')->where('nombre_puesto', '!=', '')
            ->distinct()->orderBy('nombre_puesto')->pluck('nombre_puesto');

        $meses = [];
        $solicitudes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fechaInicio = now()->subMonths($i)->startOfMonth();
            $fechaFin = now()->subMonths($i)->endOfMonth();
            $meses[] = self::MESES_ESP[$fechaInicio->format('n') - 1];
            $solicitudes[] = Dictamen::whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('estatus', 'ENVIADO')
                ->count();
        }

        return view('visor-dictamenes.public', compact(
            'dictamenes',
            'total',
            'enviados',
            'anios',
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
        return strtoupper(str_replace([' ', '-', '_', '.', ',', '/'], '', trim((string) $clave)));
    }

    private function claveCoincide(string $claveNorm, string $nombreNorm): bool
    {
        return $claveNorm !== '' && $nombreNorm !== ''
            && ($nombreNorm === $claveNorm || str_starts_with($nombreNorm, $claveNorm));
    }

    private function anotarEstadoArchivo($dictamenes, array $archivosPorAnio): void
    {
        foreach ($dictamenes as $d) {
            $clave = trim((string) $d->clave_documento);
            $d->estado_archivo = 'sin_clave';
            $d->archivos_encontrados = [];

            if ($clave === '' || in_array(strtoupper($clave), ['S/N', 'S/D'], true)) {
                continue;
            }

            $claveNorm = $this->normalizarClave($clave);
            if ($claveNorm === '') {
                continue;
            }

            $coincidencias = [];
            foreach ($archivosPorAnio as $anio => $nombres) {
                foreach ($nombres as $nombre) {
                    if ($this->claveCoincide($claveNorm, $this->normalizarClave($nombre))) {
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
