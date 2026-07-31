<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetVisitorUuid
{
    public function handle(Request $request, Closure $next): Response
    {
        $vuid = $request->cookie('_vuid');

        if (!$vuid || !Str::isUuid($vuid)) {
            $vuid = (string) Str::uuid();
        }

        $request->attributes->set('_vuid', $vuid);

        $response = $next($request);

        if (!$request->cookie('_vuid')) {
            $response->cookie('_vuid', $vuid, 365 * 10, '/', null, false, true);
        }

        return $response;
    }
}
