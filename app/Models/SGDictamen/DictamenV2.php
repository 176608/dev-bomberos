<?php
namespace App\Models\SGDictamen;

use App\Models\SGU\User;
use Illuminate\Database\Eloquent\Model;

class DictamenV2 extends Model
{
    protected $table = 'dictamenes';
    public $timestamps = true;

    protected $fillable = [
        'anio',
        'dia',
        'mes',
        'fecha_raw',
        'oficio',
        'nombre_puesto',
        'dependencia_empres',
        'asunto',
        'estatus',
        'numero_oficio_raw',
        'archivo_raw',
        'revisado_por',
        'observaciones',
        'fecha',
        'numero_oficio',
        'archivo',
        'fecha_cierre',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_cierre' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
