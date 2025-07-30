<?php
// USO INTERNO — Este modelo puede ser utilizado tanto en vistas públicas como en vistas de administrador,
// dependiendo del controlador o la vista Blade que lo utilice.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaExpressDato extends Model
{
    protected $table = 'consulta_express_dato';

    protected $fillable = [
        'ce_contenido_id',
        'concepto',
        'valor',
    ];

    public function contenido()
    {
        return $this->belongsTo(ConsultaExpressContenido::class, 'ce_contenido_id');
    }
}
