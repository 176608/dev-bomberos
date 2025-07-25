<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlyAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->username === 'admin') {
            return $next($request);
        }

        // Si no es admin, redirige a la vista pública
        return redirect('/'); // o a cualquier ruta pública que uses, como '/home'
    }
}
