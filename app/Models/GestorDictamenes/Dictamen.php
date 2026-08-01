<?php

namespace App\Models\GestorDictamenes;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bomberos\User;

class Dictamen extends Model
{
    protected $table = 'dictamenes';

    public const STATUSES = [
        'ENVIADO',
        'BORRADOR PARA FIRMA',
        'EN REVISION',
        'INFORMATIVO',
        'S/D',
    ];

    protected $fillable = [
        'anio',
        'dia',
        'mes',
        'fecha_raw',
        'oficio',
        'nombre_puesto',
        'dependencia_empres',
        'asunto',
        'estatus',
        'numero_oficio_raw',
        'archivo_raw',
        'revisado_por',
        'observaciones',
        'fecha',
        'numero_oficio',
        'archivo',
        'legacy_id',
        'fecha_cierre',
        'created_by',
        'updated_by',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
