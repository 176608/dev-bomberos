<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colonias extends Model
{
    protected $table = 'colonias';
    
    protected $primaryKey = 'IDKEY';

    protected $fillable = [
        'ID_COLO',
        'NOMBRE',
        'ETIQUETA',
        'TIPO'
    ];

    public $timestamps = false; // If the table doesn't have created_at and updated_at columns
}