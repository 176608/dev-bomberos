<?php
// USO INTERNO — Este modelo puede ser utilizado tanto en vistas públicas como en vistas de administrador,
// dependiendo del controlador o la vista Blade que lo utilice.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaExpressContenido extends Model
{
    protected $table = 'consulta_express_contenido';
    protected $primaryKey = 'ce_contenido_id'; // esto es clave
    public $incrementing = true;
    public $timestamps = true; // o false si no estás usando created_at/updated_at

    protected $fillable = [
        'ce_subtema_id',
        'ce_contenido'
    ];
}
