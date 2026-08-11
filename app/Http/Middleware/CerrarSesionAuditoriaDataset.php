<?php

namespace App\Http\Middleware;

use App\Services\GestorSIGEM\AuditoriaDatasetService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CerrarSesionAuditoriaDataset
{
    public function __construct(
        private AuditoriaDatasetService $auditoriaDatasetService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $estaEnEditorDataset = $request->routeIs(
                'sgiem.admin.cuadros.dataset*',
                'sgiem.admin.cuadros.grafica',
                'sgiem.admin.cuadros.datos-json',
            );

            if (!$estaEnEditorDataset) {
                $this->auditoriaDatasetService->cerrarTodasSesiones();
            }
        }

        return $next($request);
    }
}
