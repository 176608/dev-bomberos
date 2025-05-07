<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agregar extends Model
{
    protected $table = 'agregar';
    protected $fillable = [
        'fecha_inspeccion',
        'numero_hidrante',
        'calle',
        'y_calle'
    ];
}