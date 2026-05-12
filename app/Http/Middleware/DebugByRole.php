<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugByRole
{
    protected array $debugRoles = ['Desarrollador'];

    public function handle(Request $request, Closure $next): Response
    {
        $isDebugEnabled = auth()->check() && 
            in_array(auth()->user()->role, $this->debugRoles);

        config(['app.debug' => $isDebugEnabled]);

        if ($isDebugEnabled) {
            \Log::debug('Debug enabled for role: ' . auth()->user()->role);
        }

        return $next($request);
    }
}