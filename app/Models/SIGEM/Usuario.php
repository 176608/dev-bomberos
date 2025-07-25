<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $table = 'usuarios'; // nombre exacto de la tabla en la base de datos

    protected $fillable = [
        'nombre', 'email', 'password' // agrega los campos reales de tu tabla
    ];

    protected $hidden = ['password'];
}
