<?php

namespace App\Models\GestorDictamenes;

use Illuminate\Database\Eloquent\Model;
use App\Models\SGU\User;

class Dictamen extends Model
{
    protected $table = 'dictamenes';

    public const STATUSES = [
        'ENVIADO',
        'BORRADOR PARA FIRMA',
        'EN REVISION',
        'INFORMATIVO',
        'S/D',
    ];

    public const DESHABILITADO = 'DESHABILITADO';

    public const FILTERABLE_STATUSES = [
        'ENVIADO',
        'BORRADOR PARA FIRMA',
        'EN REVISION',
        'INFORMATIVO',
        'S/D',
    ];

    public const MESES = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    // Pre-relleno de "Oficio recibido" según el tipo de dictamen seleccionado.
    // Para agregar más autorellenos basta añadir una entrada aquí (clave = tipo en MAYÚSCULAS).
    public const AUTOFILL_OFICIO_RECIBIDO = [
        'REVISION DE ANTEPROYECTOS FRACCIONAMIENTOS' => 'PLATAFORMA FRACCIONAMIENTOS FOLIO:',
        'GENERAL' => 'PARTICULAR',
    ];

    protected $fillable = [
        'anio',
        'dia',
        'mes',
        'fecha_raw',
        'oficio_recibido',
        'tipo_dictamen',
        'nombre_puesto',
        'dependencia_empres',
        'asunto',
        'estatus',
        'numero_oficio_raw',
        'numero_oficio',
        'revisado_por',
        'observaciones',
        'fecha',
        'created_by',
        'updated_by',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function archivosLigados()
    {
        return $this->hasMany(DictamenArchivo::class, 'dictamen_id');
    }
}
