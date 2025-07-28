<?php
// Este controlador gestiona vistas restringidas del sistema SIGEM.
// Requiere autenticación y permisos de administrador.

/* <!-- -RECIEN AGREGADO 25/07/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Http\Controllers\SIGEM;

use App\Http\Controllers\SIGEM\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Verificar permisos de administrador
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if (!$user->hasRole('Administrador') && !$user->hasRole('Desarrollador')) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder al panel SIGEM.');
        }

        return view('roles.sigem_admin');
    }
    
    public function temas()
    {
        // Gestión de temas (solo admin)
        if (!auth()->check() || (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador'))) {
            return redirect()->route('login');
        }
        
        return view('sigem.admin.temas');
    }
    
    public function subtemas()
    {
        // Gestión de subtemas (solo admin)
        if (!auth()->check() || (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador'))) {
            return redirect()->route('login');
        }
        
        return view('sigem.admin.subtemas');
    }
    
    public function contenidos()
    {
        // Gestión de contenidos (solo admin)
        if (!auth()->check() || (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador'))) {
            return redirect()->route('login');
        }
        
        return view('sigem.admin.contenidos');
    }
    
    public function usuarios()
    {
        // Gestión de usuarios SIGEM (solo admin)
        if (!auth()->check() || (!auth()->user()->hasRole('Administrador') && !auth()->user()->hasRole('Desarrollador'))) {
            return redirect()->route('login');
        }
        
        return view('sigem.admin.usuarios');
    }
}
