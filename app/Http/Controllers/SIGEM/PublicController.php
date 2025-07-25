<?php
/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        // Vista pública SIGEM - accesible para todos (sin autenticación)
        return view('roles.sigem');
    }
    
    public function dashboard()
    {
        // Dashboard público con estadísticas básicas
        return view('sigem.public.dashboard');
    }
    
    public function geografico()
    {
        // Vista geográfica pública
        return view('sigem.public.geografico');
    }
    
    public function estadisticas()
    {
        // Vista de estadísticas públicas
        return view('sigem.public.estadisticas');
    }
}