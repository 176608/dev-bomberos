<?php

namespace App\Models\SGU;

use Illuminate\Database\Eloquent\Model;

class AuditoriaUsuario extends Model
{
    protected $table = 'auditoria_usuarios';
    protected $primaryKey = 'auditoria_id';

    protected $fillable = [
        'user_id',
        'usuario_id',
        'accion',
        'datos_previos',
        'datos_nuevos',
        'sesion_id',
    ];

    protected function casts(): array
    {
        return [
            'datos_previos' => 'array',
            'datos_nuevos' => 'array',
        ];
    }

    /**
     * Usuario al que se le aplicó el cambio (objetivo)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Usuario que realizó el cambio (actor)
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
