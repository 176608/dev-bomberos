<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminDev
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(auth()->user()->role, ['Administrador', 'Desarrollador'])) {
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}