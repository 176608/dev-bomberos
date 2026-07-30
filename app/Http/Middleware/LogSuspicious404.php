<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSuspicious404
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() === 404) {
            $ip = $request->ip();
            $key = 'suspicious-404:' . $ip;
            $threshold = 20;
            $window = 300;

            $attempts = Cache::get($key, 0) + 1;
            Cache::put($key, $attempts, $window);

            if ($attempts === $threshold) {
                Log::channel('suspicious404')->warning('Posible enumeración detectada', [
                    'ip' => $ip,
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path(),
                    'attempts' => $attempts,
                    'window_seconds' => $window,
                ]);
            }
        }

        return $response;
    }
}
