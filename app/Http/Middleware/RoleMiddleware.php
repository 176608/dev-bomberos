<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['Administrador', 'Desarrollador'])) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}