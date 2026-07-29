<?php

namespace App\Http\Controllers\VisorSIGEM;

use App\Models\SIGEM\Cuadro;

abstract class Controller extends \Illuminate\Routing\Controller
{
    protected function tieneCredenciales(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('Desarrollador') ||
            auth()->user()->hasRole('Estadistico')
        );
    }

    protected function esDesarrollador(): bool
    {
        return $this->tieneCredenciales();
    }

    protected function verificarAccesoCuadro(Cuadro $cuadro): ?array
    {
        if ($cuadro->publicado) return null;
        if ($this->tieneCredenciales()) return null;
        return ['error' => 'No tienes permiso para acceder a este cuadro.'];
    }
}
