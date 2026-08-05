<?php

namespace App\Models\GestorDictamenes;

use Illuminate\Database\Eloquent\Model;
use App\Models\SGU\User;

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

    public const DESHABILITADO = 'DESHABILITADO';

    public const FILTERABLE_STATUSES = [
        'ENVIADO',
        'BORRADOR PARA FIRMA',
        'EN REVISION',
        'INFORMATIVO',
        'S/D',
        'DESHABILITADO',
    ];

    protected $fillable = [
        'anio',
        'dia',
        'mes',
        'fecha_raw',
        'oficio_recibido',
        'tipo_dictamen',
        'nombre_puesto',
        'dependencia_empres',
        'asunto',
        'estatus',
        'numero_oficio_raw',
        'numero_oficio',
        'revisado_por',
        'observaciones',
        'fecha',
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

    public function archivosLigados()
    {
        return $this->hasMany(DictamenArchivo::class, 'dictamen_id');
    }
}
