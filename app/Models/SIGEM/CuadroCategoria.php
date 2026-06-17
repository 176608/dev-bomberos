<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class CuadroCategoria extends Model
{
    protected $table = 'cuadro_categoria';
    protected $primaryKey = 'categoria_id';

    protected $fillable = [
        'cuadro_id',
        'eje',
        'padre_id',
        'nombre',
        'orden',
        'tipo'
    ];

    protected $casts = [
        'categoria_id' => 'integer',
        'cuadro_id' => 'integer',
        'padre_id' => 'integer',
        'orden' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function cuadro()
    {
        return $this->belongsTo(Cuadro::class, 'cuadro_id', 'cuadro_id');
    }

    public function padre()
    {
        return $this->belongsTo(self::class, 'padre_id', 'categoria_id');
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'padre_id', 'categoria_id')
            ->orderBy('orden');
    }

    public function datos()
    {
        return $this->hasMany(CuadroDato::class, 'cat_horizontal_id', 'categoria_id')
            ->orWhere('cat_vertical_id', 'categoria_id');
    }

    public function esHorizontal()
    {
        return $this->eje === 'horizontal';
    }

    public function esVertical()
    {
        return $this->eje === 'vertical';
    }

    public function esTotal()
    {
        return $this->tipo === 'total';
    }

    public function esPromedio()
    {
        return $this->tipo === 'promedio';
    }

    public function esPorcentual()
    {
        return $this->tipo === 'porcentual';
    }
}
