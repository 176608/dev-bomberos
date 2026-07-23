<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class CuadroSeccion extends Model
{
    protected $table = 'cuadro_secciones';
    protected $primaryKey = 'seccion_id';

    protected $fillable = [
        'cuadro_id',
        'nombre',
        'orden',
        'header',
        'footer',
    ];

    protected $casts = [
        'seccion_id' => 'integer',
        'cuadro_id' => 'integer',
        'orden' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cuadro()
    {
        return $this->belongsTo(Cuadro::class, 'cuadro_id', 'cuadro_id');
    }

    public function datos()
    {
        return $this->hasMany(CuadroDato::class, 'seccion_id', 'seccion_id');
    }
}
