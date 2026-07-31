<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class PubVisita extends Model
{
    protected $table = 'pub_visita';
    protected $primaryKey = 'visita_id';
    public $timestamps = false;

    protected $fillable = [
        'visitante_id',
        'cuadro_id',
        'accion',
        'detalle',
        'origen',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'visitante_id' => 'int',
            'cuadro_id' => 'int',
        ];
    }

    public function visitante()
    {
        return $this->belongsTo(PubVisitante::class, 'visitante_id');
    }

    public function cuadro()
    {
        return $this->belongsTo(Cuadro::class, 'cuadro_id');
    }
}
