<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;

class SubtemaV2 extends Model
{
    protected $table = 'subtema_v2';
    protected $primaryKey = 'subtema_id';
    public $timestamps = false;

    protected $fillable = [
        'subtema_titulo',
        'imagen',
        'orden_indice',
        'tema_id',
        'clave_subtema'
    ];

    protected $casts = [
        'subtema_id' => 'integer',
        'tema_id' => 'integer',
        'subtema_titulo' => 'string',
        'imagen' => 'string',
        'orden_indice' => 'integer',
        'clave_subtema' => 'string'
    ];

    public function tema()
    {
        return $this->belongsTo(TemaV2::class, 'tema_id', 'tema_id');
    }

    public function cuadros()
    {
        return $this->hasMany(Cuadro::class, 'subtema_id', 'subtema_id');
    }

    public static function obtenerTodos()
    {
        return self::with('tema')->get();
    }

    public static function obtenerPorId($subtema_id)
    {
        return self::with('tema')->find($subtema_id);
    }

    public static function obtenerPorTema($tema_id)
    {
        return self::where('tema_id', $tema_id)->orderBy('orden_indice', 'asc')->get();
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

    public static function siguienteOrden($tema_id)
    {
        $ultimo = self::where('tema_id', $tema_id)
            ->orderBy('orden_indice', 'desc')
            ->first();

        return $ultimo ? $ultimo->orden_indice + 1 : 1;
    }
}
