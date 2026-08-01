<?php

namespace App\Models\GestorDictamenes;

use Illuminate\Database\Eloquent\Model;

class DictamenArchivo extends Model
{
    protected $table = 'dictamenes_archivos';

    public const UPDATED_AT = null;

    protected $fillable = [
        'dictamen_id',
        'anio',
        'nombre_archivo',
        'created_by',
    ];

    public function dictamen()
    {
        return $this->belongsTo(Dictamen::class, 'dictamen_id');
    }
}
