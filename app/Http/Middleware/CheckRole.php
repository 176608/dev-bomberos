<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // CAMBIO PRINCIPAL: Desarrollador tiene acceso a todo
        if ($user->hasRole('Desarrollador')) {
            return $next($request);
        }
        
        // Para otros roles, verificar permisos normalmente
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Si no tiene el rol requerido, redirigir según su rol actual
        if ($user->hasRole('Administrador')) {
            return redirect()->route('admin.panel');
        } elseif ($user->hasRole('Capturista')) {
            return redirect()->route('capturista.panel');
        } elseif ($user->hasRole('Desarrollador')) {
            return redirect()->route('dev.panel');
        }

        return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta página.');
    }
}