<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar el middleware de roles
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'debug.role' => \App\Http\Middleware\DebugByRole::class,
        ]);
        
        // Middleware global para debug por rol (se ejecuta en todas las rutas)
        $middleware->web(prepend: [
            \App\Http\Middleware\DebugByRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
