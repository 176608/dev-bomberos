<?php

namespace App\Http\Controllers\GestorDictamenes;

use App\Models\GestorDictamenes\AuditoriaDictamen;
use App\Models\GestorDictamenes\Dictamen;
use App\Models\GestorDictamenes\DictamenArchivo;
use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DictamenController extends Controller
{
    private const MESES_ESP = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
    private const MESES_ABREV = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

    private function camposDesdeFecha(string $fecha): array
    {
        $f = \Carbon\Carbon::parse($fecha);
        $m = (int) $f->format('m') - 1;

        return [
            'anio' => (int) $f->format('Y'),
            'dia' => (string) (int) $f->format('d'),
            'mes' => self::MESES_ABREV[$m],
            'fecha_raw' => (string) (int) $f->format('d') . ' ' . self::MESES_ESP[$m],
        ];
    }

    private function mayus(?string $valor): ?string
    {
        return $valor === null ? null : mb_strtoupper(trim($valor));
    }

    public function index(Request $request)
    {
        $query = Dictamen::query();

        if ($request->filled('estatus') && in_array($request->estatus, Dictamen::FILTERABLE_STATUSES, true)) {
            $query->where('estatus', $request->estatus);
        } else {
            $query->where('estatus', '!=', Dictamen::DESHABILITADO);
        }

        if ($request->filled('anio') && preg_match('/^\d{4}$/', $request->anio)) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('mes') && (int) $request->mes >= 1 && (int) $request->mes <= 12) {
            $query->whereMonth('fecha', (int) $request->mes);
        }

        if ($request->filled('revisado_por')) {
            $query->where('revisado_por', $request->revisado_por);
        }

        if ($request->filled('dependencia')) {
            $query->where('dependencia_empres', $request->dependencia);
        }

        if ($request->filled('tipo_dictamen')) {
            $query->where('tipo_dictamen', $request->tipo_dictamen);
        }

        $dictamenes = $query->with('archivosLigados')->orderByDesc('fecha')->get();

        $archivosPorAnio = $this->archivosDiscoPorAnio();
        $this->anotarEstadoArchivo($dictamenes, $archivosPorAnio);

        $total = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)->count();
        $conteoEstatus = [];
        foreach (Dictamen::STATUSES as $estatus) {
            $conteoEstatus[$estatus] = Dictamen::where('estatus', $estatus)->count();
        }
        $enviados = $conteoEstatus['ENVIADO'];

        $anios = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('anio')
            ->distinct()
            ->orderBy('anio')
            ->pluck('anio');

        $revisadosPor = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('revisado_por')->where('revisado_por', '!=', '')
            ->distinct()->orderBy('revisado_por')->pluck('revisado_por');

        $dependencias = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('dependencia_empres')->where('dependencia_empres', '!=', '')
            ->distinct()->orderBy('dependencia_empres')->pluck('dependencia_empres');

        $tiposDictamen = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('tipo_dictamen')->where('tipo_dictamen', '!=', '')
            ->distinct()->orderBy('tipo_dictamen')->pluck('tipo_dictamen');

        $nombresPuestos = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('nombre_puesto')->where('nombre_puesto', '!=', '')
            ->distinct()->orderBy('nombre_puesto')->pluck('nombre_puesto');

        $aniosDisponibles = collect($anios)
            ->merge(collect(array_keys($archivosPorAnio))->filter(fn ($a) => $a !== ''))
            ->unique()->sort()->values();

        $sanitizar = fn ($v) => is_string($v)
            ? preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $v)
            : $v;

        $datosGrafica = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->get(['fecha', 'estatus', 'revisado_por', 'dependencia_empres', 'nombre_puesto'])
            ->map(fn ($d) => [
                'f' => $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : null,
                'e' => $sanitizar($d->estatus),
                'r' => $sanitizar($d->revisado_por),
                'd' => $sanitizar($d->dependencia_empres),
                'n' => $sanitizar($d->nombre_puesto),
            ])->values();

        return view('gestor-dictamenes.index', compact(
            'dictamenes',
            'total',
            'conteoEstatus',
            'enviados',
            'anios',
            'aniosDisponibles',
            'revisadosPor',
            'dependencias',
            'tiposDictamen',
            'nombresPuestos',
            'datosGrafica'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'oficio_recibido' => 'required|string|max:50',
            'tipo_dictamen' => 'nullable|string|max:100',
            'numero_oficio' => 'nullable|string|max:100',
            'dependencia_empres' => 'nullable|string|max:255',
            'asunto' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(Dictamen::STATUSES)],
            'nombre_puesto' => 'nullable|string|max:255',
            'revisado_por' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $dictamen = Dictamen::create(array_merge([
            'fecha' => $request->fecha,
            'oficio_recibido' => $this->mayus($request->oficio_recibido),
            'tipo_dictamen' => $this->mayus($request->tipo_dictamen),
            'numero_oficio' => $this->mayus($request->numero_oficio),
            'dependencia_empres' => $this->mayus($request->dependencia_empres),
            'asunto' => $this->mayus($request->asunto),
            'estatus' => $this->mayus($request->estatus),
            'nombre_puesto' => $this->mayus($request->nombre_puesto),
            'revisado_por' => $this->mayus($request->revisado_por),
            'observaciones' => $this->mayus($request->observaciones),
            'created_by' => auth()->id(),
        ], $this->camposDesdeFecha($request->fecha)));
        $dictamen->refresh();

        $this->auditar($dictamen, 'CREAR', null, $this->snapshotDictamen($dictamen));

        return back()->with('success', 'Dictamen creado correctamente.');
    }

    public function update(Request $request, Dictamen $dictamen)
    {
        $request->validate([
            'fecha' => 'required|date',
            'oficio_recibido' => 'required|string|max:50',
            'tipo_dictamen' => 'nullable|string|max:100',
            'numero_oficio' => 'nullable|string|max:100',
            'dependencia_empres' => 'nullable|string|max:255',
            'asunto' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(Dictamen::STATUSES)],
            'nombre_puesto' => 'nullable|string|max:255',
            'revisado_por' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $previos = $this->snapshotDictamen($dictamen);

        $dictamen->update(array_merge([
            'fecha' => $request->fecha,
            'oficio_recibido' => $this->mayus($request->oficio_recibido),
            'tipo_dictamen' => $this->mayus($request->tipo_dictamen),
            'numero_oficio' => $this->mayus($request->numero_oficio),
            'dependencia_empres' => $this->mayus($request->dependencia_empres),
            'asunto' => $this->mayus($request->asunto),
            'estatus' => $this->mayus($request->estatus),
            'nombre_puesto' => $this->mayus($request->nombre_puesto),
            'revisado_por' => $this->mayus($request->revisado_por),
            'observaciones' => $this->mayus($request->observaciones),
            'updated_by' => auth()->id(),
        ], $this->camposDesdeFecha($request->fecha)));
        $dictamen->refresh();

        $this->auditar($dictamen, 'MODIFICAR', $previos, $this->snapshotDictamen($dictamen));

        return back()->with('success', 'Dictamen actualizado correctamente.');
    }

    public function destroy(Dictamen $dictamen)
    {
        if ($dictamen->estatus === Dictamen::DESHABILITADO) {
            return back()->with('error', 'El dictamen ya está deshabilitado.');
        }

        $previos = $this->snapshotDictamen($dictamen);

        $dictamen->update([
            'estatus' => Dictamen::DESHABILITADO,
            'updated_by' => auth()->id(),
        ]);

        $this->auditar($dictamen, 'DESHABILITAR', $previos, null);

        return back()->with('success', 'Dictamen deshabilitado correctamente.');
    }

    public function restore(Dictamen $dictamen)
    {
        if ($dictamen->estatus !== Dictamen::DESHABILITADO) {
            return back()->with('error', 'El dictamen no está deshabilitado.');
        }

        $auditoria = AuditoriaDictamen::where('dictamen_id', $dictamen->id)
            ->where('accion', 'DESHABILITAR')
            ->latest('auditoria_id')
            ->first();

        $estatusAnterior = $auditoria?->datos_previos['estatus'] ?? 'ENVIADO';

        $previos = $this->snapshotDictamen($dictamen);

        $dictamen->update([
            'estatus' => $estatusAnterior,
            'updated_by' => auth()->id(),
        ]);
        $dictamen->refresh();

        $this->auditar($dictamen, 'RESTAURAR', $previos, $this->snapshotDictamen($dictamen));

        $this->auditar($dictamen, 'RESTAURAR');

        return back()->with('success', "Dictamen restaurado correctamente (estatus: {$estatusAnterior}).");
    }

    public function deletedDictamenes()
    {
        $dictamenes = Dictamen::where('estatus', Dictamen::DESHABILITADO)
            ->with('archivosLigados')
            ->orderByDesc('fecha')
            ->get()
            ->map(function (Dictamen $d) {
                $auditoria = AuditoriaDictamen::where('dictamen_id', $d->id)
                    ->where('accion', 'DESHABILITAR')
                    ->latest('auditoria_id')
                    ->first();

                return (object) [
                    'dictamen_id' => $d->id,
                    'fecha' => $d->fecha,
                    'oficio' => $d->oficio_recibido,
                    'oficio_recibido' => $d->oficio_recibido,
                    'tipo_dictamen' => $d->tipo_dictamen,
                    'dependencia_empres' => $d->dependencia_empres,
                    'nombre_puesto' => $d->nombre_puesto,
                    'asunto' => $d->asunto,
                    'numero_oficio' => $d->numero_oficio,
                    'revisado_por' => $d->revisado_por,
                    'observaciones' => $d->observaciones,
                    'archivo' => $d->archivosLigados->count(),
                    'estatus' => $auditoria?->datos_previos['estatus'] ?? 'ENVIADO',
                    'deleted_by' => $auditoria?->user_id,
                    'deleted_at' => $auditoria?->created_at,
                ];
            })
            ->values();

        $tiposDictamen = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('tipo_dictamen')->where('tipo_dictamen', '!=', '')
            ->distinct()->orderBy('tipo_dictamen')->pluck('tipo_dictamen');

        $dependencias = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('dependencia_empres')->where('dependencia_empres', '!=', '')
            ->distinct()->orderBy('dependencia_empres')->pluck('dependencia_empres');

        $nombresPuestos = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('nombre_puesto')->where('nombre_puesto', '!=', '')
            ->distinct()->orderBy('nombre_puesto')->pluck('nombre_puesto');

        $revisadosPor = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('revisado_por')->where('revisado_por', '!=', '')
            ->distinct()->orderBy('revisado_por')->pluck('revisado_por');

        return view('gestor-dictamenes.deleted', compact(
            'dictamenes',
            'tiposDictamen',
            'dependencias',
            'nombresPuestos',
            'revisadosPor'
        ));
    }

    public function historialCambios()
    {
        $cambios = AuditoriaDictamen::with('usuario')
            ->latest('auditoria_id')
            ->limit(1000)
            ->get();

        return view('gestor-dictamenes.historial', compact('cambios'));
    }

    private function snapshotDictamen(Dictamen $dictamen): array
    {
        return [
            'fecha' => $dictamen->fecha ? \Carbon\Carbon::parse($dictamen->fecha)->format('Y-m-d') : null,
            'oficio_recibido' => $dictamen->oficio_recibido,
            'tipo_dictamen' => $dictamen->tipo_dictamen,
            'numero_oficio' => $dictamen->numero_oficio,
            'dependencia_empres' => $dictamen->dependencia_empres,
            'nombre_puesto' => $dictamen->nombre_puesto,
            'asunto' => $dictamen->asunto,
            'estatus' => $dictamen->estatus,
            'revisado_por' => $dictamen->revisado_por,
            'observaciones' => $dictamen->observaciones,
        ];
    }

    // ==================== Gestión de archivos ====================

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
            $clave = trim((string) $d->numero_oficio);
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

    public function archivosIndex(Request $request)
    {
        $porAnio = $this->archivosDiscoPorAnio();

        $dictamenesPorAnio = Dictamen::whereNotNull('anio')->get()->groupBy('anio');

        $archivos = [];
        foreach ($porAnio as $anio => $nombres) {
            foreach ($nombres as $nombre) {
                if (!preg_match('/\.(doc|docx)$/i', $nombre)) {
                    continue;
                }
                $ruta = $anio !== '' ? $anio . '/' . $nombre : $nombre;
                $ligado = 0;
                if ($anio !== '' && isset($dictamenesPorAnio[$anio])) {
                    $nombreNorm = $this->normalizarClave($nombre);
                    foreach ($dictamenesPorAnio[$anio] as $d) {
                        if ($this->claveCoincide($this->normalizarClave($d->numero_oficio), $nombreNorm)) {
                            $ligado++;
                        }
                    }
                }
                $archivos[] = [
                    'ruta' => $ruta,
                    'anio' => $anio !== '' ? $anio : null,
                    'nombre' => $nombre,
                    'ligado' => $ligado,
                ];
            }
        }

        usort($archivos, fn ($a, $b) => strcmp($a['ruta'], $b['ruta']));

        return response()->json(['archivos' => $archivos]);
    }

    public function archivoSubir(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:doc,docx|max:20480',
            'anio' => 'nullable|digits:4',
        ]);

        $file = $request->file('archivo');
        $nombre = $file->getClientOriginalName();
        $anio = $request->filled('anio') ? $request->anio : (string) date('Y');
        $reemplazar = $request->boolean('reemplazar');

        $rutaDestino = $anio !== '' ? $anio . '/' . $nombre : $nombre;

        if (Storage::disk('dictamenes')->exists($rutaDestino) && !$reemplazar) {
            return response()->json([
                'ok' => false,
                'existe' => true,
                'ruta' => $rutaDestino,
                'nombre' => $nombre,
                'mensaje' => 'Ya existe un archivo con ese nombre.',
            ], 409);
        }

        Storage::disk('dictamenes')->putFileAs($anio, $file, $nombre);

        $ligados = $this->ligarPorPrefijo($nombre, $anio);

        return response()->json([
            'ok' => true,
            'existe' => false,
            'reemplazado' => $reemplazar,
            'nombre' => $nombre,
            'anio' => $anio !== '' ? $anio : null,
            'ligados' => $ligados,
            'mensaje' => ($reemplazar ? "El archivo {$nombre} fue reemplazado." : "El archivo {$nombre} fue subido.")
                . ($ligados > 0 ? " Se ligó automáticamente a {$ligados} dictámen(es)." : ''),
        ]);
    }

    private function ligarPorPrefijo(string $nombre, string $anio): int
    {
        if ($anio === '') {
            return 0;
        }

        $archivoNorm = $this->normalizarClave($nombre);

        $candidatos = Dictamen::where('anio', (int) $anio)->get()->filter(function ($d) use ($archivoNorm) {
            return $this->claveCoincide($this->normalizarClave($d->numero_oficio), $archivoNorm);
        });

        $contador = 0;
        foreach ($candidatos as $dictamen) {
            $existe = DictamenArchivo::where('dictamen_id', $dictamen->id)
                ->where('anio', (int) $anio)
                ->where('nombre_archivo', $nombre)
                ->exists();

            if (!$existe) {
                DictamenArchivo::create([
                    'dictamen_id' => $dictamen->id,
                    'anio' => (int) $anio,
                    'nombre_archivo' => $nombre,
                    'created_by' => auth()->id(),
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    public function archivoDescargar(Request $request)
    {
        $ruta = trim((string) $request->query('archivo', ''));

        if ($ruta === '' || str_contains($ruta, '..') || str_contains($ruta, '\\')) {
            abort(404);
        }

        if (!Storage::disk('dictamenes')->exists($ruta)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('dictamenes')->download($ruta);
    }

    public function vincularArchivo(Request $request)
    {
        $request->validate([
            'dictamen_id' => 'required|integer',
            'ruta' => 'required|string',
        ]);

        $dictamen = Dictamen::findOrFail($request->dictamen_id);
        $ruta = trim($request->ruta);

        if (!preg_match('#^(\d{4})/([^/]+)$#', $ruta, $m)) {
            return response()->json(['ok' => false, 'mensaje' => 'El archivo debe estar en una carpeta de año (2025/2026/2027).'], 422);
        }

        if (!Storage::disk('dictamenes')->exists($ruta)) {
            return response()->json(['ok' => false, 'mensaje' => 'El archivo no existe en el servidor.'], 422);
        }

        $anio = (int) $m[1];
        $nombre = $m[2];

        $existe = DictamenArchivo::where('dictamen_id', $dictamen->id)
            ->where('anio', $anio)
            ->where('nombre_archivo', $nombre)
            ->exists();

        if ($existe) {
            return response()->json(['ok' => false, 'mensaje' => 'El archivo ya está ligado a este dictamen.'], 409);
        }

        DictamenArchivo::create([
            'dictamen_id' => $dictamen->id,
            'anio' => $anio,
            'nombre_archivo' => $nombre,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['ok' => true, 'mensaje' => 'Archivo ligado correctamente.']);
    }

    public function desvincularArchivo(Request $request)
    {
        $request->validate([
            'dictamen_id' => 'required|integer',
            'ruta' => 'required|string',
        ]);

        $dictamen = Dictamen::findOrFail($request->dictamen_id);

        if (!preg_match('#^(\d{4})/([^/]+)$#', $ruta = trim($request->ruta), $m)) {
            return response()->json(['ok' => false, 'mensaje' => 'Ruta de archivo inválida.'], 422);
        }

        $eliminadas = DictamenArchivo::where('dictamen_id', $dictamen->id)
            ->where('anio', (int) $m[1])
            ->where('nombre_archivo', $m[2])
            ->delete();

        return response()->json([
            'ok' => $eliminadas > 0,
            'mensaje' => $eliminadas > 0 ? 'Archivo desligado correctamente.' : 'El archivo no estaba ligado.',
        ]);
    }

    public function archivoEliminar(Request $request)
    {
        $request->validate([
            'ruta' => 'required|string',
        ]);

        $ruta = trim($request->ruta);

        if ($ruta === '' || str_contains($ruta, '..') || str_contains($ruta, '\\')) {
            return response()->json(['ok' => false, 'mensaje' => 'Ruta de archivo inválida.'], 422);
        }

        if (!Storage::disk('dictamenes')->exists($ruta)) {
            return response()->json(['ok' => false, 'mensaje' => 'El archivo no existe en el servidor.'], 422);
        }

        if (preg_match('#^(\d{4})/([^/]+)$#', $ruta, $m)) {
            DictamenArchivo::where('anio', (int) $m[1])
                ->where('nombre_archivo', $m[2])
                ->delete();
        }

        Storage::disk('dictamenes')->delete($ruta);

        return response()->json(['ok' => true, 'mensaje' => "El archivo {$ruta} fue eliminado del servidor."]);
    }

    private function auditar(Dictamen $dictamen, string $accion, ?array $previos = null, ?array $nuevos = null)
    {
        AuditoriaDictamen::create([
            'user_id' => auth()->id(),
            'dictamen_id' => $dictamen->id,
            'accion' => $accion,
            'datos_previos' => $previos,
            'datos_nuevos' => $nuevos,
        ]);
    }
}
