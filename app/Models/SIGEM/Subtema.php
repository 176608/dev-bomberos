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
        'orden_indice',
        'tema_id',
        'clave_subtema'
    ];
    
    /**
     * Casting de atributos
     */
    protected $casts = [
        'subtema_id' => 'integer',
        'tema_id' => 'integer',
        'subtema_titulo' => 'string',
        'imagen' => 'string',
        'orden_indice' => 'integer',
        'clave_subtema' => 'string'
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
    
    /**
     * NUEVA FUNCIÓN: Obtener clave efectiva con lógica de herencia y duplicados
     */
    public function obtenerClaveEfectiva()
    {
        // 1. Si tiene clave_subtema propia, verificar duplicados
        if (!empty($this->clave_subtema)) {
            // Buscar si hay otros subtemas con la misma clave
            $duplicados = self::where('clave_subtema', $this->clave_subtema)
                             ->where('subtema_id', '!=', $this->subtema_id)
                             ->orderBy('orden_indice', 'asc')
                             ->get();
            
            if ($duplicados->count() > 0) {
                // Hay duplicados, verificar si este es el de menor orden_indice
                $menorOrden = self::where('clave_subtema', $this->clave_subtema)
                                ->min('orden_indice');
                
                if ($this->orden_indice == $menorOrden) {
                    return $this->clave_subtema; // Este es el de menor orden
                } else {
                    // No es el de menor orden, usar clave del tema
                    return $this->tema ? $this->tema->clave_tema : null;
                }
            } else {
                // No hay duplicados, usar su propia clave
                return $this->clave_subtema;
            }
        }
        
        // 2. Si clave_subtema es nulo, heredar del tema
        if ($this->tema && !empty($this->tema->clave_tema)) {
            return $this->tema->clave_tema;
        }
        
        // 3. Fallback: retornar null si no hay nada
        return null;
    }

    /**
     * NUEVA FUNCIÓN: Obtener información completa de la clave
     */
    public function obtenerInfoClave()
    {
        $claveEfectiva = $this->obtenerClaveEfectiva();
        $origen = 'null';
        
        if (!empty($this->clave_subtema)) {
            // Verificar si hay duplicados
            $duplicados = self::where('clave_subtema', $this->clave_subtema)->count();
            
            if ($duplicados > 1) {
                $menorOrden = self::where('clave_subtema', $this->clave_subtema)->min('orden_indice');
                if ($this->orden_indice == $menorOrden) {
                    $origen = 'propia (menor orden)';
                } else {
                    $origen = 'heredada del tema (por duplicado)';
                }
            } else {
                $origen = 'propia';
            }
        } elseif ($this->tema && !empty($this->tema->clave_tema)) {
            $origen = 'heredada del tema';
        }
        
        return [
            'clave_efectiva' => $claveEfectiva,
            'clave_original' => $this->clave_subtema,
            'clave_tema' => $this->tema ? $this->tema->clave_tema : null,
            'origen' => $origen,
            'orden_indice' => $this->orden_indice
        ];
    }

    /**
     * Obtener todos los subtemas con información de claves
     */
    public static function obtenerTodosConClaves()
    {
        return self::with('tema')->orderBy('orden_indice', 'asc')->get()->map(function($subtema) {
            $subtema->info_clave = $subtema->obtenerInfoClave();
            return $subtema;
        });
    }
}
