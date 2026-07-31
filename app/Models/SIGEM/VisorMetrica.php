<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class VisorMetrica extends Model
{
    protected $table = 'visor_metricas';
    protected $primaryKey = 'metrica_id';
    public $timestamps = false;

    protected $fillable = [
        'cuadro_id',
        'accion',
        'origen',
        'user_id',
        'ip',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'cuadro_id' => 'int',
            'user_id' => 'int',
        ];
    }

    public function cuadro()
    {
        return $this->belongsTo(Cuadro::class, 'cuadro_id');
    }
}
