<?php
namespace App\Models\SGDictamen;

use Illuminate\Database\Eloquent\Model;

class Dictamen extends Model
{
    protected $table = 'datos_dictamen';
    public $timestamps = false;

    protected $fillable = [
        'fecha', 
        'oficio', 
        'nombre_puesto', 
        'dependencia_empres',
        'asunto', 
        'numero_oficio', 
        'revisado_por', 
        'observaciones', 
        'estatus',
        'created_by',  
        'updated_by',    
        'deleted_by'     
    ];

    protected $casts = [
        'fecha' => 'date',
        // Si agregaste los campos de fecha (created_at, updated_at) en la BD,
        // puedes descomentar estas líneas para tratarlos como objetos de fecha:
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
    ];
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el dictamen por última vez.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Relación con el usuario que eliminó el dictamen.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}