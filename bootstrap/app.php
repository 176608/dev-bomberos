<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias registrados
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'debug.role' => \App\Http\Middleware\DebugByRole::class,
            'login.throttle' => \App\Http\Middleware\LoginRateLimiter::class,
            'log.404' => \App\Http\Middleware\LogSuspicious404::class,
        ]);

        // Middleware del grupo web (append: sesión ya disponible para auth()->check())
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SetVisitorUuid::class,
            \App\Http\Middleware\DebugByRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
