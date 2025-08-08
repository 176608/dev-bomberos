<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\Bomberos;

use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;
use App\Models\Bomberos\Hidrante;
use Barryvdh\DomPDF\Facade\Pdf; // Corregir este import

class DashboardController extends Controller
{
    public function index()
    {
        // Contar hidrantes activos
        $totalHidrantes = Hidrante::where('stat', '!=', '000')->count();
        
        // Vista principal/landing page
        return view('dashboard', compact('totalHidrantes'));
    }
    
    public function buscarHidrante(Request $request)
    {
        $hidranteId = $request->input('hidrante_id');
        $totalHidrantes = Hidrante::where('stat', '!=', '000')->count();
        
        // Validar que el ID es un número entero positivo
        if (!is_numeric($hidranteId) || intval($hidranteId) <= 0) {
            return view('dashboard', [
                'error' => 'El ID del hidrante debe ser un número entero positivo.',
                'totalHidrantes' => $totalHidrantes
            ]);
        }
        
        // Buscar el hidrante
        $hidrante = Hidrante::find($hidranteId);
        
        if (!$hidrante) {
            return view('dashboard', [
                'error' => 'No se encontró ningún hidrante con el ID: ' . $hidranteId,
                'totalHidrantes' => $totalHidrantes
            ]);
        }
        
        // Si el hidrante está desactivado, solo mostrar si es administrador
        if ($hidrante->stat === '000' && (!auth()->check() || !in_array(auth()->user()->role, ['Administrador', 'Desarrollador']))) {
            return view('dashboard', [
                'error' => 'El hidrante solicitado está desactivado.',
                'totalHidrantes' => $totalHidrantes
            ]);
        }
        
        return view('dashboard', compact('hidrante', 'totalHidrantes'));
    }
    
    // En el controlador HidranteController
    public function generarPDF($id)
    {
        $hidrante = Hidrante::findOrFail($id);
        
        // Validar si el hidrante está desactivado
        if ($hidrante->stat === '000' && (!auth()->check() || !in_array(auth()->user()->role, ['Administrador', 'Desarrollador']))) {
            return redirect()->route('dashboard')->with('error', 'El hidrante solicitado está desactivado.');
        }
        
        // Cargar la vista del partial con los datos del hidrante
        $pdf = PDF::loadView('partials.hidrante-consulta-pdf', [
            'hidrante' => $hidrante,
            'readOnly' => true
        ]);
        
        // Configurar el PDF (opcional)
        $pdf->setPaper('a4', 'portrait');
        
        // Nombre descriptivo para el archivo
        $fileName = 'hidrante-'.$id.'-'.date('Y-m-d').'.pdf';
        
        // Descargar el PDF
        return $pdf->download($fileName);
    }
}