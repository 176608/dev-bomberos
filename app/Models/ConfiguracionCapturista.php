<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionCapturista extends Model
{
    protected $table = 'configuracion_capturistas';
    
    protected $fillable = [
        'user_id',
        'configuracion',
        'filtros_act',
        'resumen_id'  // Nuevo campo para guardar el tipo de resumen
    ];

    protected $casts = [
        'configuracion' => 'array',
        'filtros_act' => 'array',
        'resumen_id' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultConfig()
    {
        return [
            'calle',
            'y_calle'
        ];
    }

    public static function getDefaultFilters()
    {
        return [];
    }

    public function getConfiguracionAttribute($value)
    {
        return $value ? json_decode($value, true) : self::getDefaultConfig();
    }
    
    public function getFiltrosActAttribute($value)
    {
        return $value ? json_decode($value, true) : self::getDefaultFilters();
    }
    
    // Valor por defecto para resumen_id
    public function getResumenIdAttribute($value)
    {
        return $value !== null ? (int)$value : 0;
    }
}