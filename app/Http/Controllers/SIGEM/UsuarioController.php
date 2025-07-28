<?php
// Este controlador pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios autorizados pueden visualizar el listado de usuarios registrados.

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return view('usuarios.index', compact('usuarios'));
    }
}
