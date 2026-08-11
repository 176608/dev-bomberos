<?php

namespace App\Models\SIGEM;

use App\Models\SGU\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaDataset extends Model
{
    protected $table = 'auditoria_datasets';

    protected $primaryKey = 'auditoria_id';

    protected $fillable = [
        'user_id',
        'cuadro_id',
        'accion',
        'estado_anterior',
        'estado_nuevo',
        'resumen_cambios',
    ];

    protected $appends = ['modelo', 'modelo_id', 'sesion_id'];

    protected function casts(): array
    {
        return [
            'estado_anterior' => 'array',
            'estado_nuevo' => 'array',
            'resumen_cambios' => 'array',
        ];
    }

    public function getModeloAttribute(): string
    {
        return 'Dataset';
    }

    public function getModeloIdAttribute(): int
    {
        return $this->cuadro_id;
    }

    public function getSesionIdAttribute(): ?string
    {
        return null;
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
