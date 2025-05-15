<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//Al agregar el modelo, se puede usar para interactuar con la base de datos
class Agregar extends Model
{
    protected $table = 'agregar';
    protected $fillable = [
        'fecha_alta',
        'colonia',
        'calle',
        'y_calle',
        'oficial'
    ];
}