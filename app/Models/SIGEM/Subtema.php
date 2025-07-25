<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtema extends Model
{
    protected $table = 'consulta_express_subtema';
    protected $primaryKey = 'ce_subtema_id';
    public $timestamps = false;

    protected $fillable = [
        'ce_subtema',
        'ce_tema_id',
    ];

    public function tema()
    {
        return $this->belongsTo(Tema::class, 'ce_tema_id', 'ce_tema_id');
    }
}
