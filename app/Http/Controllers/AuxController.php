<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuxController extends Controller
{
    public function __construct()
    {
        // Use auth middleware instead of custom implementation
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !in_array(Auth::user()->role, ['Administrador', 'Desarrollador'])) {
                return redirect()->route('dashboard');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            $users = null;
            if (Auth::user()->role === 'Administrador') {
                $users = User::all();
            }
            
            return view('VistaAux', [
                'users' => $users,
                'status' => 'success',
                'message' => 'Vista cargada correctamente'
            ]);
            
        } catch (Exception $e) {
            return view('VistaAux', [
                'users' => null,
                'status' => 'error',
                'message' => "Error: " . $e->getMessage()
            ]);
        }
    }
}