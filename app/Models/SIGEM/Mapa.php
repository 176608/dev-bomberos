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
        'icono',
        'codigo_mapa'
    ];
    
    /**
     * Campos ocultos para arrays/JSON
     */
    protected $hidden = [];
    
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
     * Obtener mapa por ID
     */
    public static function obtenerPorId($mapa_id)
    {
        return self::find($mapa_id);
    }
    
    /**
     * Obtener mapas por sección
     */
    public static function obtenerPorSeccion($nombre_seccion)
    {
        return self::where('nombre_seccion', $nombre_seccion)
                  ->orderBy('nombre_mapa', 'asc')
                  ->get();
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
    
    /**
     * Buscar mapas por nombre
     */
    public static function buscarPorNombre($nombre_mapa)
    {
        return self::where('nombre_mapa', 'LIKE', "%{$nombre_mapa}%")->get();
    }
    
    /**
     * Obtener secciones únicas
     */
    public static function obtenerSecciones()
    {
        return self::select('nombre_seccion')->distinct()->get()->pluck('nombre_seccion');
    }
    
    /**
     * Validar si el mapa existe
     */
    public static function existe($mapa_id)
    {
        return self::where('mapa_id', $mapa_id)->exists();
    }
    
    /**
     * Scope para ordenar por nombre
     */
    public function scopeOrdenadoPorNombre($query)
    {
        return $query->orderBy('nombre_mapa', 'asc');
    }
    
    /**
     * Accessor para obtener la ruta completa del icono
     */
    public function getRutaIconoAttribute()
    {
        return $this->icono ? asset('imagenes/' . $this->icono) : null;
    }
    
    /**
     * Accessor para obtener el nombre formateado
     */
    public function getNombreFormateadoAttribute()
    {
        return ucfirst(strtolower($this->nombre_mapa));
    }
    
    /**
     * Obtener mapas para cartografía (método específico)
     */
    public static function obtenerParaCartografia()
    {
        return self::where('nombre_seccion', 'cartografia')
                  ->orWhere('nombre_seccion', 'LIKE', '%mapa%')
                  ->orderBy('nombre_mapa', 'asc')
                  ->get();
    }
}