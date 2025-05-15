<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class AuxController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['Administrador', 'Desarrollador'])) {
                return redirect('dashboard');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            
            // Si el usuario es administrador, obtener lista de usuarios
            $users = null;
            if (auth()->user()->role === 'Administrador') {
                $users = User::all();
            }
            
        } catch (Exception $e) {
            $message = "Error de conexión: " . $e->getMessage();
            $status = "error";
            $users = null;
        }

        return view('VistaAux', compact('message', 'status', 'users'));
    }
}