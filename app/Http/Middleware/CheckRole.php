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
        
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Si no tiene el rol requerido, redirigir según su rol actual
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.panel');
        } elseif ($user->hasRole('capturista')) {
            return redirect()->route('capturista.panel');
        } elseif ($user->hasRole('desarrollador')) {
            return redirect()->route('dev.panel');
        }

        return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta página.');
    }
}