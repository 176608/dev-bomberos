<?php

namespace App\Http\Controllers\SGU;

use App\Models\SIGEM\AuditoriaAcceso;
use App\Models\SGU\AuditoriaUsuario;
use Illuminate\View\View;

class AuditorController
{
    private const ETIQUETAS_ACCION = [
        'login' => ['Inicio de sesión', 'success'],
        'logout' => ['Cierre de sesión', 'secondary'],
        'intento_fallido' => ['Intento fallido', 'danger'],
        'primer_acceso' => ['Primer acceso', 'warning'],
        'restauracion_password' => ['Restauración de contraseña', 'info'],
    ];

    private const ETIQUETAS_DETALLE = [
        'bloqueado_rate_limit' => 'Bloqueado por límite de intentos',
        'usuario_no_encontrado' => 'Usuario no encontrado',
        'contrasena_incorrecta' => 'Contraseña incorrecta',
        'cuenta_desactivada' => 'Cuenta desactivada',
        'pin_incorrecto' => 'PIN incorrecto',
        'requiere_restauracion_password' => 'Requiere restauración de contraseña',
    ];

    public function accesos(): View
    {
        $accesos = AuditoriaAcceso::with('usuario')->orderByDesc('created_at')->get();

        return view('sgu.auditor.accesos', [
            'accesos' => $accesos,
            'etiquetas' => self::ETIQUETAS_ACCION,
            'detalles' => self::ETIQUETAS_DETALLE,
        ]);
    }

    public function usuarios(): View
    {
        $auditorias = AuditoriaUsuario::with(['usuario', 'actor'])->orderByDesc('created_at')->get();

        return view('sgu.auditor.usuarios', [
            'auditorias' => $auditorias,
        ]);
    }
}
