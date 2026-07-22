<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Controllers\GestorSIGEM\Controller;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\SubtemaV2;
use App\Models\SIGEM\AuditoriaSgiem;
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

        return view('GestorSIGEM.layout')->with([
            'crud_view' => 'GestorSIGEM.admin.dashboard',
            'auditoria' => $auditoria,
            'resumen' => $resumen,
            'modelos' => $modelos,
            'esAdmin' => $user->hasRole('Administrador'),
            'rangoActual' => $rango,
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
