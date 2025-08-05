<?php
/* <!-- -AGREGADO 04/08/2025- Archivo SIGEM - NO ELIMINAR COMENTARIO --> */
namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class ce_contenido extends Model
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'consulta_express_contenido';
    
    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'ce_contenido_id';
    
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
    public $timestamps = true;
    
    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'ce_subtema_id',
        'ce_contenido'
    ];
    
    /**
     * Obtener el subtema al que pertenece este contenido.
     */
    public function subtema()
    {
        return $this->belongsTo(ce_subtema::class, 'ce_subtema_id', 'ce_subtema_id');
    }
    
    /**
     * Obtener contenido por subtema
     */
    public static function obtenerPorSubtema($subtemaId)
    {
        return self::where('ce_subtema_id', $subtemaId)
                 ->orderBy('created_at', 'desc')
                 ->get();
    }
    
    /**
     * Obtener contenidos más recientes
     */
    public static function obtenerRecientes($limit = 5)
    {
        return self::with(['subtema', 'subtema.tema'])
                 ->orderBy('created_at', 'desc')
                 ->limit($limit)
                 ->get();
    }
    
    /**
     * Función para sanitizar y preparar contenido HTML para almacenamiento seguro
     */
    public static function prepararContenidoHTML($contenidoHTML)
    {
        // Sanitizar HTML para prevenir XSS pero permitir tablas y formato
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'table[style|class|border|cellspacing|cellpadding|width],tr[style|class],td[style|class|colspan|rowspan|width],th[style|class|colspan|rowspan|width],thead,tbody,p,b,strong,i,em,u,a[href|title],ol,ul,li,br,span[style|class],img[src|alt|title|width|height|style|class],h1,h2,h3,h4,h5,h6');
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,width,border,border-collapse');
        $purifier = new \HTMLPurifier($config);
        
        return $purifier->purify($contenidoHTML);
    }
}