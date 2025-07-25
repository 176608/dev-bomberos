<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers;

use App\Models\Hidrante;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        // No aplicamos middleware de auth aquí para permitir acceso público
    }

    /**
     * Mostrar la landing page/dashboard público
     */
    public function index()
    {
        // Si el usuario está autenticado, mostrar información personalizada
        if (auth()->check()) {
            $user = auth()->user();
            
            // Aquí puedes agregar lógica específica para usuarios autenticados
            return view('dashboard.index', compact('user'));
        }
        
        // Para usuarios no autenticados, mostrar landing page pública
        return view('dashboard.public');
    }
}