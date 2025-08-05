<?php
/* <!-- -AGREGADO 04/08/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class ce_tema extends Model
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'consulta_express_tema';
    
    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'ce_tema_id';
    
    /**
     * Indica si los IDs del modelo son auto-incrementales.
     *
     * @var bool
     */
    public $incrementing = true;
    
    /**
     * Indica si el modelo debe tener marcas de tiempo.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'tema'
    ];
    
    /**
     * Obtener los subtemas asociados con este tema.
     */
    public function subtemas()
    {
        return $this->hasMany(ce_subtema::class, 'ce_tema_id', 'ce_tema_id');
    }
    
    /**
     * Obtener todos los temas ordenados por ID
     */
    public static function obtenerTodos()
    {
        return self::orderBy('ce_tema_id', 'asc')->get();
    }
}