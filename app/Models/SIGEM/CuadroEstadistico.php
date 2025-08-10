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
     * AGREGAR: Accessor para obtener el tema a través del subtema
     */
    public function getTemaAttribute()
    {
        return $this->subtema ? $this->subtema->tema : null;
    }
    
    /**
     * Obtener todos los cuadros estadísticos con relaciones CORREGIDAS
     */
    public static function obtenerTodos()
    {
        return self::with(['subtema.tema']) // CAMBIO: cargar tema a través de subtema
                  ->orderBy('codigo_cuadro', 'asc')
                  ->get();
    }
    
    /**
     * Obtener cuadro estadístico por ID con relaciones CORREGIDAS
     */
    public static function obtenerPorId($cuadro_estadistico_id)
    {
        return self::with(['subtema.tema']) // CAMBIO: cargar tema a través de subtema
                  ->find($cuadro_estadistico_id);
    }
    
    /**
     * Obtener cuadros por subtema CORREGIDA
     */
    public static function obtenerPorSubtema($subtema_id)
    {
        return self::with(['subtema.tema']) // CAMBIO: cargar tema a través de subtema
                  ->where('subtema_id', $subtema_id)
                  ->orderBy('codigo_cuadro', 'asc')
                  ->get();
    }
    
    /**
     * Crear nuevo cuadro estadístico
     * Equivalente a insertar_cuadro_estadistico() del archivo PHP
     */
    public static function crear($datos)
    {
        return self::create([
            'codigo_cuadro' => $datos['codigo_cuadro'],
            'cuadro_estadistico_titulo' => $datos['nombre_cuadro'],
            'tema_id' => $datos['tema_id'],
            'subtema_id' => $datos['subtema_id'],
            'cuadro_estadistico_subtitulo' => $datos['subtitulo'] ?? null,
            'excel_file' => $datos['excel_file'] ?? null,
            'pdf_file' => $datos['pdf_file'] ?? null,
            'permite_grafica' => $datos['permite_grafica'] ?? false,
            'tipo_grafica_permitida' => $datos['tipo_grafica_permitida'] ?? null,
            'mapa_id' => $datos['mapa_id'] ?? null
        ]);
    }
    
    /**
     * Insertar cuadro estadístico (método específico para compatibilidad)
     */
    public static function insertarCuadroEstadistico($codigo_cuadro, $nombre_cuadro, $tema_id, $subtema_id)
    {
        return self::create([
            'codigo_cuadro' => $codigo_cuadro,
            'cuadro_estadistico_titulo' => $nombre_cuadro,
            'tema_id' => $tema_id,
            'subtema_id' => $subtema_id
        ]);
    }
    
    /**
     * Actualizar cuadro estadístico existente
     * Equivalente a actualizarCuadroEstadistico() del archivo PHP
     */
    public function actualizar($datos)
    {
        return $this->update($datos);
    }
    
    /**
     * Actualizar cuadro estadístico (método específico para compatibilidad)
     */
    public static function actualizarCuadroEstadistico($cuadro_estadistico_id, $codigo, $titulo, $tema_id, $subtema_id)
    {
        $cuadro = self::find($cuadro_estadistico_id);
        if ($cuadro) {
            return $cuadro->update([
                'codigo_cuadro' => $codigo,
                'cuadro_estadistico_titulo' => $titulo,
                'tema_id' => $tema_id,
                'subtema_id' => $subtema_id
            ]);
        }
        return false;
    }
    
    /**
     * Eliminar cuadro estadístico
     */
    public function eliminar()
    {
        return $this->delete();
    }
    
    /**
     * Eliminar cuadro estadístico por ID
     * Equivalente a eliminarSubtemaPorId() del archivo PHP (nombre incorrecto en original)
     */
    public static function eliminarPorId($cuadro_estadistico_id)
    {
        $cuadro = self::find($cuadro_estadistico_id);
        if ($cuadro) {
            return $cuadro->delete();
        }
        return false;
    }
    
    /**
     * Buscar cuadros por código
     */
    public static function buscarPorCodigo($codigo_cuadro)
    {
        return self::with(['tema', 'subtema'])
                  ->where('codigo_cuadro', 'LIKE', "%{$codigo_cuadro}%")
                  ->orderBy('codigo_cuadro', 'asc')
                  ->get();
    }
    
    /**
     * Buscar cuadros por título
     */
    public static function buscarPorTitulo($titulo)
    {
        return self::with(['tema', 'subtema'])
                  ->where('cuadro_estadistico_titulo', 'LIKE', "%{$titulo}%")
                  ->orderBy('codigo_cuadro', 'asc')
                  ->get();
    }
    
    
    
    /**
     * Accessor para obtener el código formateado
     */
    public function getCodigoFormateadoAttribute()
    {
        return strtoupper($this->codigo_cuadro);
    }
    
    /**
     * Método para obtener el siguiente código disponible por tema
     */
    public static function siguienteCodigo($tema_id)
    {
        $tema = Tema::find($tema_id);
        if (!$tema) {
            return null;
        }
        
        // Obtener el último código para este tema
        $ultimoCuadro = self::where('tema_id', $tema_id)
                           ->orderBy('codigo_cuadro', 'desc')
                           ->first();
        
        if (!$ultimoCuadro) {
            return $tema_id . '.1'; // Primer cuadro del tema
        }
        
        // Extraer el número del código y incrementar
        $partes = explode('.', $ultimoCuadro->codigo_cuadro);
        $numero = isset($partes[1]) ? intval($partes[1]) + 1 : 1;
        
        return $tema_id . '.' . $numero;
    }
}