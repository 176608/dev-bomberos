<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colonias extends Model
{
    protected $table = 'colonias';
    
    protected $primaryKey = 'IDKEY';

    protected $fillable = [
        'FECHAUBICAIMIP',
        'NOMBRE',
        'TIPO'
    ];

    public $timestamps = false; // If the table doesn't have created_at and updated_at columns
}