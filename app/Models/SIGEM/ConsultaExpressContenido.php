<?php

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
