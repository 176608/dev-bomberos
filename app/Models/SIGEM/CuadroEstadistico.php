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
        'excel_formated_file', /* Nuevo campo ya en base de datos */
        'permite_grafica',
        'pie_pagina'
    ];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'cuadro_estadistico_id' => 'integer',
        'subtema_id' => 'integer',
        'permite_grafica' => 'boolean'
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
    
    /**
     * Obtener nombre original del archivo Excel
     */
    public function getNombreOriginalExcelAttribute()
    {
        if (!$this->excel_file) return null;
        
        // Remover el prefijo del código y timestamp para obtener el nombre original
        $nombreArchivo = $this->excel_file;
        $codigoCuadro = $this->codigo_cuadro;
        
        // Patrón: codigo_cuadro_nombreOriginal_timestamp.extension
        $patron = '/^' . preg_quote($codigoCuadro, '/') . '_(.+)_\d+\.(xlsx|xls)$/';
        
        if (preg_match($patron, $nombreArchivo, $matches)) {
            return $matches[1] . '.' . $matches[2];
        }
        
        return $nombreArchivo; // Fallback al nombre completo
    }
    
    /**
     * Obtener nombre original del archivo PDF
     */
    public function getNombreOriginalPdfAttribute()
    {
        if (!$this->pdf_file) return null;
        
        // Remover el prefijo del código y timestamp para obtener el nombre original
        $nombreArchivo = $this->pdf_file;
        $codigoCuadro = $this->codigo_cuadro;
        
        // Patrón: codigo_cuadro_nombreOriginal_timestamp.pdf
        $patron = '/^' . preg_quote($codigoCuadro, '/') . '_(.+)_\d+\.pdf$/';
        
        if (preg_match($patron, $nombreArchivo, $matches)) {
            return $matches[1] . '.pdf';
        }
        
        return $nombreArchivo; // Fallback al nombre completo
    }

public function getTipoGraficaPermitidaAttribute($value)
{
    // Si ya es array (por el cast), devuélvelo
    if (is_array($value)) {
        return $value;
    }

    // Si es null o vacío, devuelve array vacío
    if (is_null($value) || $value === '') {
        return [];
    }

    // Si es JSON válido, decodifica
    $decoded = json_decode($value, true);
    if (is_array($decoded)) {
        return $decoded;
    }

    // Si es CSV, sepáralo
    if (is_string($value)) {
        // Quitar corchetes y comillas si las tiene
        $clean = trim($value, "[]");
        $clean = str_replace('"', '', $clean);
        $clean = str_replace("'", '', $clean);
        $parts = array_map('trim', explode(',', $clean));
        return array_filter($parts, fn($v) => $v !== '');
    }

    // Fallback
    return [];
}

}