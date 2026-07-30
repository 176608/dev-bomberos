<?php

namespace App\Http\Middleware;

use App\Models\SIGEM\AuditoriaAcceso;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->status) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Tu cuenta está desactivada.');
        }

        if ($user->hasRole('Desarrollador')) {
            Log::warning('CheckRole: Desarrollador bypass', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
                'required_roles' => $roles,
            ]);

            AuditoriaAcceso::create([
                'user_id' => $user->id,
                'accion' => 'dev_bypass',
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);

            return $next($request);
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        $redirects = [
            'Administrador'             => 'sigem.admin.index',
            'Desarrollador'             => 'admin.panel',
            'Capturista'                => 'capturista.panel',
            'Registrador'               => 'registrador.panel',
            'Administrador Dictamenes'  => 'sg-dictamen.index',
            'Editor Dictamenes'         => 'sg-dictamen.index',
            'Estadistico'               => 'sgiem.admin.index',
        ];

        foreach ($redirects as $role => $route) {
            if ($user->hasRole($role)) {
                return redirect()->route($route);
            }
        }

        return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta página.');
    }
}
