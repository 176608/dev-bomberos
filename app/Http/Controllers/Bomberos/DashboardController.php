<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\Bomberos;

use App\Http\Controllers\Bomberos\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Vista principal/landing page
        return view('dashboard'); // o la vista que corresponda
    }
}