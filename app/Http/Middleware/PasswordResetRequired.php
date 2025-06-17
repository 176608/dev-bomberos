<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordResetRequired
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->log_in_status > 0 && 
            !$request->is('password/reset') && !$request->is('logout')) {
            return redirect()->route('password.reset.form');
        }

        return $next($request);
    }
}