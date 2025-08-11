<?php
/* <!-- -AGREGADO 04/08/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class ce_tema extends Model
{
    /**
     * La tabla asociada con el modelo.
     */
    protected $table = 'consulta_express_tema';
    
    /**
     * La clave primaria asociada con la tabla.
     */
    protected $primaryKey = 'ce_tema_id';
    
    /**
     * Indica si los IDs del modelo son auto-incrementales.
     */
    public $incrementing = true;
    
    /**
     * Indica si el modelo debe tener marcas de tiempo.
     */
    public $timestamps = false;
    
    /**
     * Los atributos que son asignables en masa.
     */
    protected $fillable = [
        'tema'
    ];
    
    /**
     * Relación con subtemas
     */
    public function subtemas()
    {
        return $this->hasMany(ce_subtema::class, 'ce_tema_id', 'ce_tema_id');
    }
    
    /**
     * Obtener todos los temas CE ordenados
     */
    public static function obtenerTodos()
    {
        return self::orderBy('tema', 'asc')->get();
    }
    
    /**
     * Obtener tema por ID
     */
    public static function obtenerPorId($ce_tema_id)
    {
        return self::find($ce_tema_id);
    }
    
    /**
     * Crear nuevo tema CE
     */
    public static function crear($datos)
    {
        return self::create($datos);
    }
    
    /**
     * Actualizar tema CE
     */
    public function actualizar($datos)
    {
        return $this->update($datos);
    }
    
    /**
     * Eliminar tema CE
     */
    public function eliminar()
    {
        return $this->delete();
    }
}