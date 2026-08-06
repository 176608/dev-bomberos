<?php

namespace App\Models\GestorDictamenes;

use Illuminate\Database\Eloquent\Model;

class AuditoriaDictamen extends Model
{
    protected $table = 'auditoria_dictamenes';

    protected $primaryKey = 'auditoria_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'dictamen_id',
        'accion',
        'datos_previos',
        'datos_nuevos',
    ];

    protected function casts(): array
    {
        return [
            'datos_previos' => 'array',
            'datos_nuevos' => 'array',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\SGU\User::class, 'user_id');
    }
}
