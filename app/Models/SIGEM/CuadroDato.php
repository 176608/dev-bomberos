<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;
use App\Models\SIGEM\Traits\AuditableSgiem;

class CuadroDato extends Model
{
    use AuditableSgiem;
    protected $table = 'cuadro_datos';
    protected $primaryKey = 'dato_id';

    protected $fillable = [
        'cuadro_id',
        'cat_horizontal_id',
        'cat_vertical_id',
        'valor',
        'valor_crudo',
        'fila',
        'columna'
    ];

    protected $casts = [
        'dato_id' => 'integer',
        'cuadro_id' => 'integer',
        'cat_horizontal_id' => 'integer',
        'cat_vertical_id' => 'integer',
        'fila' => 'integer',
        'columna' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function cuadro()
    {
        return $this->belongsTo(Cuadro::class, 'cuadro_id', 'cuadro_id');
    }

    public function categoriaHorizontal()
    {
        return $this->belongsTo(CuadroCategoria::class, 'cat_horizontal_id', 'categoria_id');
    }

    public function categoriaVertical()
    {
        return $this->belongsTo(CuadroCategoria::class, 'cat_vertical_id', 'categoria_id');
    }

    public function getValorNumericoAttribute()
    {
        if (is_null($this->valor)) return null;
        $limpio = str_replace(['$', ',', '%', ' '], '', $this->valor);
        return is_numeric($limpio) ? (float) $limpio : null;
    }

    public function esValido()
    {
        return !is_null($this->valor)
            && $this->valor !== ''
            && !in_array(strtolower($this->valor), ['n/a', 'na', 's/inf', 'sin informacion', '-']);
    }
}
