<?php

namespace App\Models\SGU\Traits;

use App\Models\SGU\AuditoriaUsuario;
use Illuminate\Support\Facades\Auth;

/**
 * Registra en `auditoria_usuarios` los cambios hechos a un usuario
 * desde el sistema (crear/actualizar/eliminar), igual que AuditableSgiem
 * pero solo para la entidad User.
 */
trait AuditableUsuario
{
    protected static function bootAuditableUsuario()
    {
        static::created(function ($model) {
            static::registrarAuditoria($model, 'crear', null, static::sanitizar($model->toArray()));
        });

        static::updated(function ($model) {
            $previos = $model->getOriginal();
            $nuevos = $model->getChanges();
            static::registrarAuditoria($model, 'actualizar', static::sanitizar($previos), static::sanitizar($nuevos));
        });

        static::deleted(function ($model) {
            static::registrarAuditoria($model, 'eliminar', static::sanitizar($model->toArray()), null);
        });
    }

    /**
     * Excluye campos sensibles (hashes de contraseña/PIN) del registro de auditoría.
     */
    protected static function sanitizar(array $datos): array
    {
        unset($datos['password'], $datos['remember_token'], $datos['initial_token']);
        return $datos;
    }

    protected static function registrarAuditoria($model, string $accion, ?array $previos, ?array $nuevos): void
    {
        if (!Auth::check()) {
            return;
        }

        AuditoriaUsuario::create([
            'user_id'       => Auth::id(),
            'usuario_id'    => $model->getKey(),
            'accion'        => $accion,
            'datos_previos' => $previos,
            'datos_nuevos'  => $nuevos,
            'sesion_id'     => session()->getId(),
        ]);
    }
}
