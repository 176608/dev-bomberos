<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    /**
     * Rutas públicas que NO deben llevar headers no-cache:
     * permiten caché del navegador (visores públicos, consultor, biblioteca y flujo auth público).
     */
    protected array $publicPaths = [
        'consultor*',
        'hidrante-pdf*',
        'login*',
        'check-session',
        'password*',
        'sigem-v2*',
        'VisorDictamenes',
        'biblioteca*',
        'search*',
        'catalogo',
        'sobre-la-biblioteca',
    ];

    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is(...$this->publicPaths)) {
            return $response;
        }

        // Prevenir caché en páginas protegidas (compatible con StreamedResponse y BinaryFileResponse)
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}