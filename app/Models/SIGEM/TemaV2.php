<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;
use App\Models\SIGEM\Traits\AuditableSgiem;

class TemaV2 extends Model
{
    use AuditableSgiem;

    protected $table = 'tema_v2';
    protected $primaryKey = 'tema_id';
    public $timestamps = false;

    protected $fillable = [
        'tema_titulo',
        'orden_indice',
        'clave_tema',
        'publicado',
        'color',
        'icono'
    ];

    protected $casts = [
        'tema_id' => 'integer',
        'tema_titulo' => 'string',
        'orden_indice' => 'integer',
        'clave_tema' => 'string',
        'publicado' => 'boolean',
        'color' => 'string',
        'icono' => 'string'
    ];

    public function subtemas()
    {
        return $this->hasMany(SubtemaV2::class, 'tema_id', 'tema_id')
            ->orderBy('orden_indice');
    }

    public static function obtenerTodos()
    {
        return self::all();
    }

    public static function obtenerPorId($tema_id)
    {
        return self::find($tema_id);
    }

    public static function crear($datos)
    {
        return self::create($datos);
    }

    public function actualizar($datos)
    {
        return $this->update($datos);
    }

    public function eliminar()
    {
        return $this->delete();
    }
}
