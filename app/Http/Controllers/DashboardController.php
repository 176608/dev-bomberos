<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers;

use App\Models\Hidrante;

class DashboardController extends Controller
{
    public function __construct()
    {
        // No aplicamos middleware de auth aquí para permitir acceso público
    }

    public function index()
    {
        $registros = Hidrante::all();
        return view('dashboard', compact('registros'));
    }
}