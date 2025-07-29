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
    protected $primaryKey = 'tema_id';
    
    /**
     * Indica si el modelo debe manejar timestamps automáticamente
     */
    public $timestamps = false;
    
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'tema_titulo',
        'nombre_archivo',
        'orden_indice',
        'clave_tema' // AGREGAR: nuevo campo
    ];
    
    /**
     * Campos ocultos para arrays/JSON
     */
    protected $hidden = [];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'tema_id' => 'integer',
        'tema_titulo' => 'string',
        'nombre_archivo' => 'string',
        'orden_indice' => 'integer',
        'clave_tema' => 'string' // AGREGAR: nuevo campo
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
    public static function obtenerPorId($tema_id)
    {
        return self::find($tema_id);
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
    public static function eliminarPorId($tema_id)
    {
        $tema = self::find($tema_id);
        if ($tema) {
            return $tema->delete();
        }
        return false;
    }
    
    /**
     * Buscar temas por nombre
     */
    public static function buscarPorTitulo($tema_titulo)
    {
        return self::where('tema_titulo', 'LIKE', "%{$tema_titulo}%")->get();
    }
    
    
    /**
     * Scope para ordenar por nombre
     */
    public function scopeOrdenadoPorTitulo($query)
    {
        return $query->orderBy('tema_titulo', 'asc');
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
    public static function existe($tema_id)
    {
        return self::where('tema_id ', $tema_id)->exists();
    }

    /**
     * Relación: Un tema tiene muchos subtemas
     */
    public function subtemas()
    {
        return $this->hasMany(Subtema::class, 'tema_id', 'tema_id')
                   ->orderBy('orden_indice', 'asc');
    }

    /**
     * Obtener tema con sus subtemas
     */
    public static function obtenerConSubtemas($tema_id)
    {
        return self::with('subtemas')->find($tema_id);
    }

    /**
     * Obtener todos los temas con subtemas
     */
    public static function obtenerTodosConSubtemas()
    {
        return self::with('subtemas')->orderBy('orden_indice', 'asc')->get();
    }

    /**
     * Obtener tema con sus subtemas aplicando lógica de claves
     */
    public static function obtenerConSubtemasYClaves($tema_id)
    {
        $tema = self::with(['subtemas' => function($query) {
            $query->orderBy('orden_indice', 'asc');
        }])->find($tema_id);
        
        if ($tema) {
            // Aplicar lógica de claves a los subtemas
            foreach ($tema->subtemas as $subtema) {
                $subtema->clave_efectiva = $subtema->obtenerClaveEfectiva();
            }
        }
        
        return $tema;
    }

    /**
     * Obtener todos los temas con subtemas y lógica de claves
     */
    public static function obtenerTodosConSubtemasYClaves()
    {
        $temas = self::with(['subtemas' => function($query) {
            $query->orderBy('orden_indice', 'asc');
        }])->orderBy('orden_indice', 'asc')->get();
        
        // Aplicar lógica de claves
        foreach ($temas as $tema) {
            foreach ($tema->subtemas as $subtema) {
                $subtema->clave_efectiva = $subtema->obtenerClaveEfectiva();
            }
        }
        
        return $temas;
    }
}
