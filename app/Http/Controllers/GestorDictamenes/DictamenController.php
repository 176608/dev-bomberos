<?php

namespace App\Http\Controllers\GestorDictamenes;

use App\Models\GestorDictamenes\Dictamen;
use App\Models\GestorDictamenes\DictamenArchivo;
use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DictamenController extends Controller
{
    private const MESES_ESP = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

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

        if ($request->filled('revisado_por')) {
            $query->where('revisado_por', $request->revisado_por);
        }

        if ($request->filled('dependencia')) {
            $query->where('dependencia_empres', $request->dependencia);
        }

        if ($request->filled('nombre_puesto')) {
            $query->where('nombre_puesto', $request->nombre_puesto);
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

        $nombresPuestos = Dictamen::where('estatus', '!=', Dictamen::DESHABILITADO)
            ->whereNotNull('nombre_puesto')->where('nombre_puesto', '!=', '')
            ->distinct()->orderBy('nombre_puesto')->pluck('nombre_puesto');

        $aniosDisponibles = collect($anios)
            ->merge(collect(array_keys($archivosPorAnio))->filter(fn ($a) => $a !== ''))
            ->unique()->sort()->values();

        $sanitizar = fn ($v) => is_string($v)
            ? preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $v)
            : $v;

        $datosGrafica = Dictamen::get(['fecha', 'estatus', 'revisado_por', 'dependencia_empres', 'nombre_puesto'])
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
            'nombresPuestos',
            'datosGrafica'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'numero_oficio_raw' => 'required|string|max:255',
            'clave_documento' => 'nullable|string|max:100',
            'dependencia_empres' => 'nullable|string|max:255',
            'asunto' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(Dictamen::STATUSES)],
            'nombre_puesto' => 'nullable|string|max:255',
            'revisado_por' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $dictamen = Dictamen::create([
            'fecha' => $request->fecha,
            'numero_oficio_raw' => $request->numero_oficio_raw,
            'clave_documento' => $request->clave_documento,
            'dependencia_empres' => $request->dependencia_empres,
            'asunto' => $request->asunto,
            'estatus' => $request->estatus,
            'nombre_puesto' => $request->nombre_puesto,
            'revisado_por' => $request->revisado_por,
            'observaciones' => $request->observaciones,
            'created_by' => auth()->id(),
        ]);
        $dictamen->refresh();

        $this->auditar($dictamen, 'CREAR');

        return back()->with('success', 'Dictamen creado correctamente.');
    }

    public function update(Request $request, Dictamen $dictamen)
    {
        $request->validate([
            'fecha' => 'required|date',
            'numero_oficio_raw' => 'required|string|max:255',
            'clave_documento' => 'nullable|string|max:100',
            'dependencia_empres' => 'nullable|string|max:255',
            'asunto' => 'required|string|max:255',
            'estatus' => ['required', Rule::in(Dictamen::STATUSES)],
            'nombre_puesto' => 'nullable|string|max:255',
            'revisado_por' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $dictamen->update([
            'fecha' => $request->fecha,
            'numero_oficio_raw' => $request->numero_oficio_raw,
            'clave_documento' => $request->clave_documento,
            'dependencia_empres' => $request->dependencia_empres,
            'asunto' => $request->asunto,
            'estatus' => $request->estatus,
            'nombre_puesto' => $request->nombre_puesto,
            'revisado_por' => $request->revisado_por,
            'observaciones' => $request->observaciones,
            'updated_by' => auth()->id(),
        ]);
        $dictamen->refresh();

        $this->auditar($dictamen, 'MODIFICAR');

        return back()->with('success', 'Dictamen actualizado correctamente.');
    }

    public function destroy(Dictamen $dictamen)
    {
        if ($dictamen->estatus === Dictamen::DESHABILITADO) {
            return back()->with('error', 'El dictamen ya está deshabilitado.');
        }

        $this->auditar($dictamen, 'DESHABILITAR', deleted: true);

        $dictamen->update([
            'estatus' => Dictamen::DESHABILITADO,
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Dictamen deshabilitado correctamente.');
    }

    public function restore(Dictamen $dictamen)
    {
        if ($dictamen->estatus !== Dictamen::DESHABILITADO) {
            return back()->with('error', 'El dictamen no está deshabilitado.');
        }

        $snapshot = DB::table('dictamen_audit_log')
            ->where('dictamen_id', $dictamen->id)
            ->whereNotNull('deleted_at')
            ->latest('id')
            ->first();

        $estatusAnterior = $snapshot?->estatus ?: 'ENVIADO';

        $dictamen->update([
            'estatus' => $estatusAnterior,
            'updated_by' => auth()->id(),
        ]);
        $dictamen->refresh();

        $this->auditar($dictamen, 'RESTAURAR');

        return back()->with('success', "Dictamen restaurado correctamente (estatus: {$estatusAnterior}).");
    }

    public function deletedDictamenes()
    {
        $cols = ['dictamen_id', 'fecha', 'oficio', 'dependencia_empres', 'nombre_puesto', 'asunto',
            'numero_oficio', 'revisado_por', 'observaciones', 'estatus', 'deleted_by', 'deleted_at'];
        if ($this->columnaAudit('archivo')) {
            $cols[] = 'archivo';
        }
        if ($this->columnaAudit('accion')) {
            $cols[] = 'accion';
        }

        $dictamenes = DB::table('dictamen_audit_log')
            ->select($cols)
            ->whereNotNull('deleted_at')
            ->orderByDesc('deleted_at')
            ->get();

        return view('gestor-dictamenes.deleted', compact('dictamenes'));
    }

    public function historialCambios()
    {
        $cols = ['id', 'dictamen_id', 'estatus', 'dependencia_empres', 'asunto'];
        $cols[] = $this->columnaAudit('created_at') ? 'created_at' : 'deleted_at AS created_at';
        $cols[] = ($this->columnaAudit('numero_oficio_raw') ? 'numero_oficio_raw' : 'numero_oficio') . ' AS numero_oficio_raw';
        foreach (['created_by', 'updated_by', 'deleted_by'] as $colUsuario) {
            if ($this->columnaAudit($colUsuario)) {
                $cols[] = $colUsuario;
            }
        }
        if ($this->columnaAudit('accion')) {
            $cols[] = 'accion';
        }

        $cambios = DB::table('dictamen_audit_log')
            ->select($cols)
            ->orderByDesc('id')
            ->limit(1000)
            ->get();

        return view('gestor-dictamenes.historial', compact('cambios'));
    }

    private function columnaAudit(string $col): bool
    {
        return Schema::hasColumn('dictamen_audit_log', $col);
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
                        if ($this->claveCoincide($this->normalizarClave($d->clave_documento), $nombreNorm)) {
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
        $anio = $request->filled('anio') ? $request->anio : '';
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
            return $this->claveCoincide($this->normalizarClave($d->clave_documento), $archivoNorm);
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

    private function auditar(Dictamen $dictamen, string $accion, bool $deleted = false)
    {
        $mapa = [
            'dictamen_id' => $dictamen->id,
            'accion' => $accion,
            'anio' => $dictamen->anio,
            'dia' => $dictamen->dia,
            'mes' => $dictamen->mes,
            'fecha_raw' => $dictamen->fecha_raw,
            'oficio' => $dictamen->oficio,
            'nombre_puesto' => $dictamen->nombre_puesto,
            'dependencia_empres' => $dictamen->dependencia_empres,
            'asunto' => $dictamen->asunto,
            'estatus' => $dictamen->estatus,
            'numero_oficio_raw' => $dictamen->numero_oficio_raw,
            'archivo_raw' => $dictamen->clave_documento,
            'revisado_por' => $dictamen->revisado_por,
            'observaciones' => $dictamen->observaciones,
            'fecha' => $dictamen->fecha,
            'archivo' => $dictamen->clave_documento,
            'created_by' => $dictamen->created_by,
            'updated_by' => $dictamen->updated_by,
            'deleted_by' => $deleted ? auth()->id() : null,
            'deleted_at' => $deleted ? now() : null,
        ];

        $data = [];
        foreach ($mapa as $col => $valor) {
            if (Schema::hasColumn('dictamen_audit_log', $col)) {
                $data[$col] = $valor;
            }
        }

        DB::table('dictamen_audit_log')->insert($data);
    }
}
