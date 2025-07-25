<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    protected $table = 'consulta_express_tema';
    protected $primaryKey = 'ce_tema_id';
    public $timestamps = false;

    protected $fillable = [
        'tema'
    ];
}
