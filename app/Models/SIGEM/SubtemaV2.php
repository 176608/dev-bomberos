<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;
use App\Models\SIGEM\Traits\AuditableSgiem;

class SubtemaV2 extends Model
{
    use AuditableSgiem;
    protected $table = 'subtema_v2';
    protected $primaryKey = 'subtema_id';
    public $timestamps = false;

    protected $fillable = [
        'subtema_titulo',
        'imagen',
        'orden_indice',
        'tema_id',
        'clave_subtema',
        'publicado'
    ];

    protected $casts = [
        'subtema_id' => 'integer',
        'tema_id' => 'integer',
        'subtema_titulo' => 'string',
        'imagen' => 'string',
        'orden_indice' => 'integer',
        'clave_subtema' => 'string',
        'publicado' => 'boolean'
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

    public function obtenerClaveEfectiva()
    {
        if (!empty($this->clave_subtema)) {
            $duplicados = self::where('clave_subtema', $this->clave_subtema)
                ->where('subtema_id', '!=', $this->subtema_id)
                ->orderBy('orden_indice', 'asc')
                ->get();

            if ($duplicados->count() > 0) {
                $menorOrden = self::where('clave_subtema', $this->clave_subtema)
                    ->min('orden_indice');

                if ($this->orden_indice == $menorOrden) {
                    return $this->clave_subtema;
                } else {
                    return $this->tema ? $this->tema->clave_tema : null;
                }
            } else {
                return $this->clave_subtema;
            }
        }

        if ($this->tema && !empty($this->tema->clave_tema)) {
            return $this->tema->clave_tema;
        }

        return null;
    }

    public function obtenerInfoClave()
    {
        $claveEfectiva = $this->obtenerClaveEfectiva();
        $origen = 'null';

        if (!empty($this->clave_subtema)) {
            $duplicados = self::where('clave_subtema', $this->clave_subtema)->count();

            if ($duplicados > 1) {
                $menorOrden = self::where('clave_subtema', $this->clave_subtema)->min('orden_indice');
                if ($this->orden_indice == $menorOrden) {
                    $origen = 'propia (menor orden)';
                } else {
                    $origen = 'heredada del tema (por duplicado)';
                }
            } else {
                $origen = 'propia';
            }
        } elseif ($this->tema && !empty($this->tema->clave_tema)) {
            $origen = 'heredada del tema';
        }

        return [
            'clave_efectiva' => $claveEfectiva,
            'clave_original' => $this->clave_subtema,
            'clave_tema' => $this->tema ? $this->tema->clave_tema : null,
            'origen' => $origen,
            'orden_indice' => $this->orden_indice
        ];
    }

    public static function obtenerTodosConClaves()
    {
        return self::with('tema')->orderBy('orden_indice', 'asc')->get()->map(function($subtema) {
            $subtema->info_clave = $subtema->obtenerInfoClave();
            return $subtema;
        });
    }
}
