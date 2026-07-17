<?php

namespace App\Models\SIGEM\Traits;

use App\Models\SIGEM\AuditoriaSgiem;
use Illuminate\Support\Facades\Auth;

trait AuditableSgiem
{
    protected static function bootAuditableSgiem()
    {
        static::created(function ($model) {
            static::registrarAuditoria($model, 'crear', null, $model->toArray());
        });

        static::updated(function ($model) {
            $previos = $model->getOriginal();
            $nuevos = $model->getChanges();
            static::registrarAuditoria($model, 'actualizar', $previos, $nuevos);
        });

        static::deleted(function ($model) {
            static::registrarAuditoria($model, 'eliminar', $model->toArray(), null);
        });
    }

    protected static function registrarAuditoria($model, string $accion, $previos, $nuevos)
    {
        if (!Auth::check()) {
            return;
        }

        AuditoriaSgiem::create([
            'user_id' => Auth::id(),
            'modelo' => class_basename($model),
            'modelo_id' => $model->getKey(),
            'accion' => $accion,
            'datos_previos' => $previos,
            'datos_nuevos' => $nuevos,
        ]);
    }
}
