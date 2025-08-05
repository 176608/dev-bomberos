<?php
/* <!-- -AGREGADO 04/08/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class ce_subtema extends Model
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'consulta_express_subtema';
    
    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'ce_subtema_id';
    
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
        'ce_tema_id',
        'ce_subtema'
    ];
    
    /**
     * Obtener el tema al que pertenece este subtema.
     */
    public function tema()
    {
        return $this->belongsTo(ce_tema::class, 'ce_tema_id', 'ce_tema_id');
    }
    
    /**
     * Obtener los contenidos asociados con este subtema.
     */
    public function contenidos()
    {
        return $this->hasMany(ce_contenido::class, 'ce_subtema_id', 'ce_subtema_id');
    }
    
    /**
     * Obtener subtemas por tema
     */
    public static function obtenerPorTema($temaId)
    {
        return self::where('ce_tema_id', $temaId)
                 ->orderBy('ce_subtema_id', 'asc')
                 ->get();
    }
}