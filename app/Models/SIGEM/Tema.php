<?php
// filepath: c:\Users\Zizek\Desktop\Repositorio\dev-bomberos\app\Models\SIGEM\Tema.php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    /**
     * Tabla asociada al modelo
     */
    protected $table = 'tema';
    
    /**
     * Clave primaria de la tabla
     */
    protected $primaryKey = 'id';
    
    /**
     * Indica si el modelo debe manejar timestamps automáticamente
     */
    public $timestamps = false;
    
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'nombre',
        'nombre_archivo',
        'descripcion'
    ];
    
    /**
     * Campos ocultos para arrays/JSON
     */
    protected $hidden = [];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'id' => 'integer',
        'nombre' => 'string',
        'nombre_archivo' => 'string',
        'descripcion' => 'string'
    ];
    
    /**
     * Obtener todos los temas
     */
    public static function obtenerTodos()
    {
        return self::all();
    }
    
    /**
     * Obtener tema por ID
     */
    public static function obtenerPorId($id)
    {
        return self::find($id);
    }
    
    /**
     * Crear nuevo tema
     */
    public static function crear($datos)
    {
        return self::create($datos);
    }
    
    /**
     * Actualizar tema existente
     */
    public function actualizar($datos)
    {
        return $this->update($datos);
    }
    
    /**
     * Eliminar tema
     */
    public function eliminar()
    {
        return $this->delete();
    }
    
    /**
     * Eliminar tema por ID (método estático)
     */
    public static function eliminarPorId($id)
    {
        $tema = self::find($id);
        if ($tema) {
            return $tema->delete();
        }
        return false;
    }
    
    /**
     * Buscar temas por nombre
     */
    public static function buscarPorNombre($nombre)
    {
        return self::where('nombre', 'LIKE', "%{$nombre}%")->get();
    }
    
    /**
     * Obtener temas activos (si hay campo de estado)
     */
    public static function obtenerActivos()
    {
        return self::all(); // Ajustar si hay campo de estado
    }
    
    /**
     * Scope para ordenar por nombre
     */
    public function scopeOrdenadoPorNombre($query)
    {
        return $query->orderBy('nombre', 'asc');
    }
    
    /**
     * Accessor para obtener el nombre formateado
     */
    public function getNombreFormateadoAttribute()
    {
        return ucfirst(strtolower($this->nombre));
    }
    
    /**
     * Validar si el tema existe
     */
    public static function existe($id)
    {
        return self::where('id', $id)->exists();
    }
}
