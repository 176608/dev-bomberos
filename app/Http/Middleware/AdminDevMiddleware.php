<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminDevMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['Administrador', 'Desarrollador'])) {
            return redirect()->route('dashboard');
        }
        
        return $next($request);
    }
}