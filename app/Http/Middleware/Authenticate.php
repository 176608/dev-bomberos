<?php
// Este middleware pertenece al sistema SIGEM.
// Se encarga de redirigir a los usuarios no autenticados al login antes de acceder a rutas protegidas.

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return url('/login.php'); // ← Enlace directo a tu archivo PHP clásico
        }
    }
}
