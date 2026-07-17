<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\GestorSIGEM;

use App\Http\Controllers\GestorSIGEM\Controller;
use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\TemaV2;
use App\Models\SIGEM\SubtemaV2;
use App\Models\SIGEM\AuditoriaSgiem;
use App\Services\SecureFileUpload;

class AdminController extends Controller
{
    protected SecureFileUpload $fileUploader;

    public function __construct()
    {
        $this->fileUploader = new SecureFileUpload();
    }

    public function index()
    {
        // Verificar permisos de administrador
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->hasRole('Administrador') && !$user->hasRole('Desarrollador') && !$user->hasRole('Estadistico')) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder al panel SIGEM.');
        }

        $auditoria = AuditoriaSgiem::with('usuario')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

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
            'esAdmin' => $user->hasRole('Administrador'),
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
