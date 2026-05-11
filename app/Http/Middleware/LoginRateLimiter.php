<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiter
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login:' . $request->ip();
        $maxAttempts = 5;
        $decaySeconds = 300;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Demasiados intentos de inicio de sesión. Intenta de nuevo en ' . ceil($seconds / 60) . ' minuto(s).',
                'retry_after' => $seconds
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }

    public static function clearRateLimit(string $ip): void
    {
        RateLimiter::clear('login:' . $ip);
    }

    public static function getAttempts(string $ip): int
    {
        return RateLimiter::attempts('login:' . $ip);
    }
}