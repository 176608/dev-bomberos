<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class Subtema extends Model
{
    /**
     * Tabla asociada al modelo
     */
    protected $table = 'subtema';
    
    /**
     * Clave primaria de la tabla
     */
    protected $primaryKey = 'subtema_id';
    
    /**
     * Indica si el modelo debe manejar timestamps automáticamente
     */
    public $timestamps = false;
    
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'subtema_titulo',
        'imagen',
        'orden_indice'
    ];
    
    /**
     * Campos ocultos para arrays/JSON
     */
    protected $hidden = [];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'subtema_id' => 'integer',
        'tema_id' => 'integer',
        'subtema_titulo' => 'string',
        'imagen' => 'string',
        'orden_indice' => 'integer'
    ];
    
    /**
     * Relación: Un subtema pertenece a un tema
     */
    public function tema()
    {
        return $this->belongsTo(Tema::class, 'tema_id', 'tema_id');
    }
    
    /**
     * Obtener todos los subtemas
     */
    public static function obtenerTodos()
    {
        return self::with('tema')->get();
    }
    
    /**
     * Obtener subtema por ID
     */
    public static function obtenerPorId($subtema_id)
    {
        return self::with('tema')->find($subtema_id);
    }
    
    /**
     * Obtener subtemas por tema_id
     */
    public static function obtenerPorTema($tema_id)
    {
        return self::where('tema_id', $tema_id)->orderBy('orden_indice', 'asc')->get();
    }
    
    /**
     * Crear nuevo subtema
     */
    public static function crear($datos)
    {
        return self::create($datos);
    }
    
    /**
     * Actualizar subtema existente
     */
    public function actualizar($datos)
    {
        return $this->update($datos);
    }
    
    /**
     * Eliminar subtema
     */
    public function eliminar()
    {
        return $this->delete();
    }
    
    /**
     * Eliminar subtema por ID (método estático)
     */
    public static function eliminarPorId($subtema_id)
    {
        $subtema = self::find($subtema_id);
        if ($subtema) {
            return $subtema->delete();
        }
        return false;
    }
    
    /**
     * Buscar subtemas por título
     */
    public static function buscarPorTitulo($subtema_titulo)
    {
        return self::where('subtema_titulo', 'LIKE', "%{$subtema_titulo}%")
                  ->with('tema')
                  ->get();
    }
    
    /**
     * Scope para ordenar por orden_indice
     */
    public function scopeOrdenadoPorIndice($query)
    {
        return $query->orderBy('orden_indice', 'asc');
    }
    
    /**
     * Scope para ordenar por título
     */
    public function scopeOrdenadoPorTitulo($query)
    {
        return $query->orderBy('subtema_titulo', 'asc');
    }
    
    /**
     * Accessor para obtener el título formateado
     */
    public function getTituloFormateadoAttribute()
    {
        return ucfirst(strtolower($this->subtema_titulo));
    }
    
    /**
     * Validar si el subtema existe
     */
    public static function existe($subtema_id)
    {
        return self::where('subtema_id', $subtema_id)->exists();
    }
    
    /**
     * Obtener siguiente orden_indice para un tema
     */
    public static function siguienteOrden($tema_id)
    {
        $ultimo = self::where('tema_id', $tema_id)
                     ->orderBy('orden_indice', 'desc')
                     ->first();
        
        return $ultimo ? $ultimo->orden_indice + 1 : 1;
    }
    
    /**
     * Validar si un tema tiene subtemas
     */
    public static function tieneTema($tema_id)
    {
        return self::where('tema_id', $tema_id)->exists();
    }
}
