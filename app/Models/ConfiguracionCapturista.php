<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionCapturista extends Model
{
    protected $table = 'configuracion_capturistas';
    
    protected $fillable = [
        'user_id',
        'configuracion'
    ];

    protected $casts = [
        'configuracion' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultConfig()
    {
        return [
            'id',
            'fecha_inspeccion',
            'calle',
            'y_calle',
            'colonia',
            'marca',
            'acciones'
        ];
    }

    public function getConfiguracionAttribute($value)
    {
        return $value ? json_decode($value, true) : self::getDefaultConfig();
    }
}