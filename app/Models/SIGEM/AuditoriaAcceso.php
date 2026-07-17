<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class AuditoriaAcceso extends Model
{
    protected $table = 'auditoria_accesos';
    protected $primaryKey = 'acceso_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'accion',
        'ip',
        'created_at',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\Bomberos\User::class, 'user_id');
    }
}
