<?php
/* <!-- -AGREGADO 04/08/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class ce_subtema extends Model
{
    /**
     * La tabla asociada con el modelo.
     */
    protected $table = 'consulta_express_subtema';
    
    /**
     * La clave primaria asociada con la tabla.
     */
    protected $primaryKey = 'ce_subtema_id';
    
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
        'ce_tema_id',
        'ce_subtema'
    ];
    
    /**
     * Relación con tema padre
     */
    public function tema()
    {
        return $this->belongsTo(ce_tema::class, 'ce_tema_id', 'ce_tema_id');
    }
    
    /**
     * Relación con contenidos
     */
    public function contenidos()
    {
        return $this->hasMany(ce_contenido::class, 'ce_subtema_id', 'ce_subtema_id');
    }
    
    /**
     * Obtener todos los subtemas CE con relaciones
     */
    public static function obtenerTodos()
    {
        return self::with('tema')
                  ->orderBy('ce_subtema', 'asc')
                  ->get();
    }
    
    /**
     * Obtener subtema por ID
     */
    public static function obtenerPorId($ce_subtema_id)
    {
        return self::with('tema')->find($ce_subtema_id);
    }
    
    /**
     * Obtener subtemas por tema
     */
    public static function obtenerPorTema($ce_tema_id)
    {
        return self::where('ce_tema_id', $ce_tema_id)
                  ->orderBy('ce_subtema', 'asc')
                  ->get();
    }
    
    /**
     * Crear nuevo subtema CE
     */
    public static function crear($datos)
    {
        return self::create($datos);
    }
    
    /**
     * Actualizar subtema CE
     */
    public function actualizar($datos)
    {
        return $this->update($datos);
    }
    
    /**
     * Eliminar subtema CE
     */
    public function eliminar()
    {
        return $this->delete();
    }
}