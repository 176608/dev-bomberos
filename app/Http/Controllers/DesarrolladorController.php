<?php
/* <!-- Archivo Bomberos - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers;

use App\Models\Colonias;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DesarrolladorController extends Controller
{
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'Desarrollador') {
            return redirect()->route('dashboard');
        }

        $colonias = Colonias::all();
        return view('roles.desarrollador', compact('colonias'));
    }
}