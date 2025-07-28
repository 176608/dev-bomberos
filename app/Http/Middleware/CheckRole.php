<?php
// Este middleware pertenece a una funcionalidad restringida del sistema SIGEM/BOMBEROS.
// Controla el acceso a rutas según el rol del usuario autenticado.

/*ARCHIVO BOMBEROS - NO ELIMINAR COMENTARIO */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Debug logging
        Log::info('CheckRole middleware executed', [
            'user_authenticated' => auth()->check(),
            'user_role' => auth()->check() ? auth()->user()->role : 'not authenticated',
            'required_roles' => $roles,
            'url' => $request->url()
        ]);

        if (!auth()->check()) {
            Log::info('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // CAMBIO: Desarrollador tiene acceso a todo
        if ($user->hasRole('Desarrollador')) {
            Log::info('Desarrollador access granted');
            return $next($request);
        }
        
        // Para otros roles, verificar permisos normalmente
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                Log::info('Role access granted', ['role' => $role]);
                return $next($request);
            }
        }

        // Si llegamos aquí, no tiene permisos
        Log::info('Access denied, redirecting based on role');

        // Redirigir según su rol actual
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
