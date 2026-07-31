<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class PubVisitante extends Model
{
    protected $table = 'pub_visitante';
    protected $primaryKey = 'visitante_id';
    public $timestamps = false;

    protected $fillable = [
        'vuid',
        'es_bot',
        'user_id',
        'ip_hash',
        'ip_bruta',
        'primer_visita',
        'ultima_visita',
        'total_visitas',
    ];

    protected function casts(): array
    {
        return [
            'es_bot' => 'bool',
            'user_id' => 'int',
            'total_visitas' => 'int',
        ];
    }

    public function visitas()
    {
        return $this->hasMany(PubVisita::class, 'visitante_id');
    }
}
