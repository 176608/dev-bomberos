<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Controllers\GestorSIGEM\Controller;
use App\Http\Controllers\SGU\DashboardController as SguDashboardController;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\SubtemaV2;
use App\Models\SIGEM\AuditoriaSgiem;
use App\Models\SIGEM\PubVisitante;
use App\Models\SIGEM\PubVisita;
use App\Services\SecureFileUpload;
use Illuminate\Http\Request;

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
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder al panel SIGEM.');
        }

        $rango = in_array($request->rango, ['hoy', 'semanal', 'mensual', 'todos']) ? $request->rango : 'hoy';

        $query = AuditoriaSgiem::with('usuario');

        if ($rango === 'hoy') {
            $query->whereDate('created_at', today());
        } elseif ($rango === 'semanal') {
            $query->where('created_at', '>=', now()->subWeek());
        } elseif ($rango === 'mensual') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        $auditoria = $query->orderBy('created_at', 'desc')->take(200)->get();

        $modelos = AuditoriaSgiem::distinct()->pluck('modelo')->sort()->values();

        $resumen = [
            'total_temas' => TemaV2::count(),
            'total_subtemas' => SubtemaV2::count(),
            'total_cuadros' => Cuadro::count(),
            'total_auditoria' => AuditoriaSgiem::count(),
        ];

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
            'auditoria' => $auditoria,
            'resumen' => $resumen,
            'modelos' => $modelos,
            'esAdmin' => $user->hasRole('Administrador'),
            'rangoActual' => $rango,
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

    public function detalleAuditoria($id)
    {
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
