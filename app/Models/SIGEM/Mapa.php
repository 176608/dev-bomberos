<?php
namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class Mapa extends Model
{
    /**
     * Tabla asociada al modelo
     */
    protected $table = 'mapas';
    
    /**
     * Clave primaria de la tabla
     */
    protected $primaryKey = 'mapa_id';
    
    /**
     * Indica si el modelo debe manejar timestamps automáticamente
     */
    public $timestamps = false;
    
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'nombre_seccion',
        'nombre_mapa',
        'descripcion',
        'enlace',
        'icono', /* Icono tipo png */
        'codigo_mapa'
    ];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'mapa_id' => 'integer',
        'nombre_seccion' => 'string',
        'nombre_mapa' => 'string',
        'descripcion' => 'string',
        'enlace' => 'string',
        'icono' => 'string',
        'codigo_mapa' => 'string'
    ];
    
    /**
     * Obtener todos los mapas
     */
    public static function obtenerTodos()
    {
        return self::orderBy('nombre_mapa', 'asc')->get();
    }
    
    /**
     * Obtener mapas para cartografía
     */
    public static function obtenerParaCartografia()
    {
        return self::all(); // Por ahora todos los mapas
    }
    
    /**
     * Obtener mapa por ID
     */
    public static function obtenerPorId($mapa_id)
    {
        return self::find($mapa_id);
    }
    
    /**
     * Crear nuevo mapa
     */
    public static function crear($datos)
    {
        return self::create($datos);
    }
    
    /**
     * Actualizar mapa existente
     */
    public function actualizar($datos)
    {
        return $this->update($datos);
    }
    
    /**
     * Eliminar mapa
     */
    public function eliminar()
    {
        return $this->delete();
    }
    
    /**
     * Eliminar mapa por ID
     */
    public static function eliminarPorId($mapa_id)
    {
        $mapa = self::find($mapa_id);
        if ($mapa) {
            return $mapa->delete();
        }
        return false;
    }
    
}