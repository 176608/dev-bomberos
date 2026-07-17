<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class AuditoriaSgiem extends Model
{
    protected $table = 'auditoria_sgiem';
    protected $primaryKey = 'auditoria_id';

    protected $fillable = [
        'user_id',
        'modelo',
        'modelo_id',
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
        return $this->belongsTo(\App\Models\Bomberos\User::class, 'user_id');
    }
}
