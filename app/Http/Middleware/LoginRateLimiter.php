<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiter
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login-flood:' . $request->ip();
        $maxAttempts = 10;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            Log::warning('Login flood detectado', [
                'ip' => $request->ip(),
                'retry_after' => $seconds,
            ]);

            return response()->json([
                'message' => 'Demasiadas solicitudes. Intenta de nuevo en ' . $seconds . ' segundos.',
                'retry_after' => $seconds,
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, $decaySeconds);

        return $next($request);
    }
}
