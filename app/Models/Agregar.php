<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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