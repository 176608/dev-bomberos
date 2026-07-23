<?php

namespace App\Models\SIGEM;

use Illuminate\Database\Eloquent\Model;
use App\Models\SIGEM\Traits\AuditableSgiem;

class Cuadro extends Model
{
    use AuditableSgiem;
    protected $table = 'cuadro_v2';
    protected $primaryKey = 'cuadro_id';

    protected $fillable = [
        'subtema_id',
        'codigo_cuadro',
        'c_titulo',
        'c_subtitulo',
        'publicado',
        'tipo_mapa_pdf',
        'permite_grafica',
        'tipos_grafica_permitida',
        'cabecera_gen',
        'piepagina_gen',
        'pivot_label'
    ];

    protected $casts = [
        'cuadro_id' => 'integer',
        'subtema_id' => 'integer',
        'publicado' => 'boolean',
        'tipo_mapa_pdf' => 'boolean',
        'permite_grafica' => 'boolean',
        'tipos_grafica_permitida' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function subtema()
    {
        return $this->belongsTo(SubtemaV2::class, 'subtema_id', 'subtema_id');
    }

    public function getTemaAttribute()
    {
        return $this->subtema ? $this->subtema->tema : null;
    }

    public function categorias()
    {
        return $this->hasMany(CuadroCategoria::class, 'cuadro_id', 'cuadro_id');
    }

    public function categoriasHorizontales()
    {
        return $this->categorias()->where('eje', 'horizontal');
    }

    public function categoriasVerticales()
    {
        return $this->categorias()->where('eje', 'vertical');
    }

    public function datos()
    {
        return $this->hasMany(CuadroDato::class, 'cuadro_id', 'cuadro_id');
    }

    public function secciones()
    {
        return $this->hasMany(CuadroSeccion::class, 'cuadro_id', 'cuadro_id')->orderBy('orden');
    }

    public static function obtenerTodos()
    {
        return self::with(['subtema.tema', 'categorias', 'datos'])
            ->orderBy('codigo_cuadro', 'asc')
            ->get();
    }

    public static function obtenerPorId($cuadro_id)
    {
        return self::with(['subtema.tema', 'categorias', 'datos'])
            ->find($cuadro_id);
    }

    public static function obtenerPorSubtema($subtema_id)
    {
        return self::with(['subtema.tema'])
            ->where('subtema_id', $subtema_id)
            ->orderBy('codigo_cuadro', 'asc')
            ->get();
    }

    public static function publicados()
    {
        return self::where('publicado', true)
            ->orderBy('codigo_cuadro', 'asc');
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
