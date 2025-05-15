<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role']);  // Changed from 'check.role' to 'role'
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