<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class CuadroEstadistico extends Model
{
    /**
     * Tabla asociada al modelo
     */
    protected $table = 'cuadro_estadistico';
    
    /**
     * Clave primaria de la tabla
     */
    protected $primaryKey = 'cuadro_estadistico_id';
    
    /**
     * Indica si el modelo debe manejar timestamps automáticamente
     */
    public $timestamps = false;
    
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'subtema_id',
        'codigo_cuadro',
        'cuadro_estadistico_titulo',
        'cuadro_estadistico_subtitulo',
        'excel_file',
        'pdf_file',
        'permite_grafica',
        'tipo_grafica_permitida',
        'eje_vertical_mchart',
        'pie_pagina',
        'mapa_id',
        'inventir_eje_vertical_horizontal'
    ];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'cuadro_estadistico_id' => 'integer',
        'subtema_id' => 'integer',
        'codigo_cuadro' => 'string',
        'cuadro_estadistico_titulo' => 'string',
        'cuadro_estadistico_subtitulo' => 'string',
        'excel_file' => 'string',
        'pdf_file' => 'string',
        'permite_grafica' => 'boolean',
        'tipo_grafica_permitida' => 'string',
        'eje_vertical_mchart' => 'string',
        'pie_pagina' => 'string',
        'mapa_id' => 'integer',
        'inventir_eje_vertical_horizontal' => 'boolean'
    ];
    
    /**
     * Relación con Subtema
     */
    public function subtema()
    {
        return $this->belongsTo(Subtema::class, 'subtema_id', 'subtema_id');
    }
    
    /**
     * Accessor para obtener el tema a través del subtema
     */
    public function getTemaAttribute()
    {
        return $this->subtema ? $this->subtema->tema : null;
    }
    
    /**
     * Obtener todos los cuadros estadísticos con relaciones
     */
    public static function obtenerTodos()
    {
        return self::with(['subtema.tema'])
                  ->orderBy('codigo_cuadro', 'asc')
                  ->get();
    }
    
    /**
     * Obtener cuadro estadístico por ID con relaciones
     */
    public static function obtenerPorId($cuadro_estadistico_id)
    {
        return self::with(['subtema.tema'])
                  ->find($cuadro_estadistico_id);
    }
    
    /**
     * Obtener cuadros por subtema
     */
    public static function obtenerPorSubtema($subtema_id)
    {
        return self::with(['subtema.tema'])
                  ->where('subtema_id', $subtema_id)
                  ->orderBy('codigo_cuadro', 'asc')
                  ->get();
    }
    
    /**
     * Crear nuevo cuadro estadístico
     */
    public static function crear($datos)
    {
        return self::create($datos);
    }
    
    /**
     * Actualizar cuadro estadístico existente
     */
    public function actualizar($datos)
    {
        return $this->update($datos);
    }
    
    /**
     * Eliminar cuadro estadístico
     */
    public function eliminar()
    {
        return $this->delete();
    }
}