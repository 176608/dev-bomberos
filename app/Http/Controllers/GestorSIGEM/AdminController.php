<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Controllers\GestorSIGEM\Controller;
use App\Http\Controllers\SGU\DashboardController as SguDashboardController;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\SubtemaV2;
use App\Models\SIGEM\ce_tema;
use App\Models\SIGEM\ce_subtema;
use App\Models\SIGEM\ce_contenido;
use App\Models\SIGEM\AuditoriaSgiem;
use App\Models\SIGEM\AuditoriaDataset;
use App\Models\SIGEM\PubVisitante;
use App\Models\SIGEM\PubVisita;
use App\Services\SecureFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    protected SecureFileUpload $fileUploader;

    public function __construct()
    {
        $this->fileUploader = new SecureFileUpload();
    }

    public function index(Request $request)
    {
        // Verificar permisos de administrador
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->hasRole('Administrador') && !$user->hasRole('Desarrollador') && !$user->hasRole('Estadistico')) {
            return redirect()->route('sgu.admin.index')->with('error', 'No tienes permisos para acceder al panel SIGEM.');
        }

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        if (!$desde && !$hasta) {
            $desde = now()->subDays(30)->format('Y-m-d');
            $hasta = now()->format('Y-m-d');
        }

        $visitas = PubVisita::query()
            ->when($desde, fn ($q) => $q->whereDate('pub_visita.created_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('pub_visita.created_at', '<=', $hasta));

        $audEventos = (clone $visitas)->count();

        $audEventosPorDia = (clone $visitas)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha');

        $audTopAcciones = (clone $visitas)
            ->selectRaw('accion, COUNT(*) as total')
            ->groupBy('accion')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($a) => [
                'label' => SguDashboardController::ETIQUETAS_ACCION[$a->accion] ?? $a->accion,
                'total' => (int) $a->total,
            ]);

        $audTopCuadros = (clone $visitas)
            ->selectRaw('pub_visita.cuadro_id, cuadro_v2.codigo_cuadro, cuadro_v2.c_titulo, COUNT(*) as total')
            ->leftJoin('cuadro_v2', 'cuadro_v2.cuadro_id', '=', 'pub_visita.cuadro_id')
            ->whereNotNull('pub_visita.cuadro_id')
            ->groupBy('pub_visita.cuadro_id', 'cuadro_v2.codigo_cuadro', 'cuadro_v2.c_titulo')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $audPngDescargas = (clone $visitas)->where('accion', 'grafica_png')->count();

        $audVisitantesTotales = PubVisitante::count();
        $audVisitantesNuevos = PubVisitante::whereDate('primer_visita', '>=', $desde)
            ->whereDate('primer_visita', '<=', $hasta)->count();
        $audVisitantesActivos = PubVisitante::whereDate('ultima_visita', '>=', $desde)
            ->whereDate('ultima_visita', '<=', $hasta)->count();
        $audBots = PubVisitante::where('es_bot', true)->count();
        $audHumanos = PubVisitante::where('es_bot', false)->count();

        $audUltimasVisitas = (clone $visitas)->with(['visitante', 'cuadro'])
            ->orderByDesc('created_at')->get();

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.dashboard',
            'esAdmin' => $user->hasRole('Administrador'),
            'desde' => $desde,
            'hasta' => $hasta,
            'audEventos' => $audEventos,
            'audVisitantesTotales' => $audVisitantesTotales,
            'audVisitantesNuevos' => $audVisitantesNuevos,
            'audVisitantesActivos' => $audVisitantesActivos,
            'audBots' => $audBots,
            'audHumanos' => $audHumanos,
            'audTopAcciones' => $audTopAcciones,
            'audPngDescargas' => $audPngDescargas,
            'audUltimasVisitas' => $audUltimasVisitas,
            'audDiasLabels' => $audEventosPorDia->keys()->map(fn ($f) => \Carbon\Carbon::parse($f)->format('d/m'))->values(),
            'audDiasData' => $audEventosPorDia->values(),
            'audCuadrosChart' => $audTopCuadros->map(fn ($c) => [
                'label' => $c->codigo_cuadro . ' — ' . mb_strimwidth($c->c_titulo ?? '', 0, 40, '…'),
                'total' => (int) $c->total,
            ])->reverse()->values(),
        ]);
    }

    public function cambios(Request $request)
    {
        // Verificar permisos de administrador
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->hasRole('Administrador') && !$user->hasRole('Desarrollador') && !$user->hasRole('Estadistico')) {
            return redirect()->route('sgu.admin.index')->with('error', 'No tienes permisos para acceder al panel SIGEM.');
        }

        $rango = in_array($request->rango, ['hoy', 'semanal', 'mensual', 'todos']) ? $request->rango : 'semanal';

        $querySgiem = AuditoriaSgiem::with('usuario');
        $queryDataset = Schema::hasTable('auditoria_datasets')
            ? AuditoriaDataset::with('usuario')
            : null;

        if ($rango === 'hoy') {
            $querySgiem->whereDate('created_at', today());
            if ($queryDataset) $queryDataset->whereDate('created_at', today());
        } elseif ($rango === 'semanal') {
            $querySgiem->where('created_at', '>=', now()->subWeek());
            if ($queryDataset) $queryDataset->where('created_at', '>=', now()->subWeek());
        } elseif ($rango === 'mensual') {
            $querySgiem->where('created_at', '>=', now()->subDays(30));
            if ($queryDataset) $queryDataset->where('created_at', '>=', now()->subDays(30));
        }

        $auditoria = $querySgiem->orderBy('created_at', 'desc')->get();
        if ($queryDataset) {
            $auditoria = $auditoria->concat($queryDataset->orderBy('created_at', 'desc')->get())
                ->sortByDesc('created_at')
                ->values();
        }

        $modelos = AuditoriaSgiem::distinct()->pluck('modelo')->sort()->values();
        if ($queryDataset && AuditoriaDataset::exists()) {
            $modelos = $modelos->concat(['Dataset'])->unique()->sort()->values();
        }

        $titulos = $this->resolverTitulos($auditoria);

        $resumen = [
            'total_temas' => TemaV2::count(),
            'total_subtemas' => SubtemaV2::count(),
            'total_cuadros' => Cuadro::count(),
            'total_auditoria' => AuditoriaSgiem::count() + ($queryDataset ? AuditoriaDataset::count() : 0),
        ];

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.cambios',
            'auditoria' => $auditoria,
            'resumen' => $resumen,
            'modelos' => $modelos,
            'titulos' => $titulos,
            'esAdmin' => $user->hasRole('Administrador'),
            'rangoActual' => $rango,
        ]);
    }

    private function resolverTitulos($auditoria): array
    {
        $porModelo = [];
        foreach ($auditoria as $log) {
            $porModelo[$log->modelo][$log->modelo_id] = true;
        }

        $titulos = [];
        foreach ($porModelo as $modelo => $ids) {
            $ids = array_keys($ids);

            $titulos[$modelo] = match ($modelo) {
                'TemaV2' => TemaV2::whereIn('tema_id', $ids)->pluck('tema_titulo', 'tema_id')->all(),
                'SubtemaV2' => SubtemaV2::whereIn('subtema_id', $ids)->pluck('subtema_titulo', 'subtema_id')->all(),
                'Cuadro', 'Dataset' => Cuadro::whereIn('cuadro_id', $ids)->pluck('c_titulo', 'cuadro_id')->all(),
                'ce_tema' => ce_tema::whereIn('ce_tema_id', $ids)->pluck('tema', 'ce_tema_id')->all(),
                'ce_subtema' => ce_subtema::whereIn('ce_subtema_id', $ids)->pluck('ce_subtema', 'ce_subtema_id')->all(),
                'ce_contenido' => ce_contenido::whereIn('ce_contenido_id', $ids)->pluck('titulo_tabla', 'ce_contenido_id')->all(),
                default => [],
            };
        }
        return $titulos;
    }

    public function detalleAuditoria(Request $request, $id)
    {
        $tipo = $request->query('tipo');

        if ($tipo === 'dataset') {
            $log = Schema::hasTable('auditoria_datasets') ? AuditoriaDataset::find($id) : null;

            if (!$log) {
                return response()->json(['error' => 'No encontrado'], 404);
            }

            return response()->json([
                'datos_previos' => $log->estado_anterior,
                'datos_nuevos' => $log->estado_nuevo,
                'resumen_cambios' => $log->resumen_cambios,
                'modelo' => 'Dataset',
                'accion' => $log->accion,
            ]);
        }

        $log = AuditoriaSgiem::find($id);

        if (!$log) {
            return response()->json(['error' => 'No encontrado'], 404);
        }

        return response()->json([
            'datos_previos' => $log->datos_previos,
            'datos_nuevos' => $log->datos_nuevos,
            'modelo' => $log->modelo,
            'accion' => $log->accion,
        ]);
    }
}
