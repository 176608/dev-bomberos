<?php
namespace App\Models\Bomberos;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class CambioEnHidrante extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'historial_cambios_h';

    /**
     * La clave primaria asociada a la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id_cambio_h';

    /**
     * Indica si el modelo debe tener marcas de tiempo.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'id_user',
        'id_hidrante',
        'campo',
        'old',
        'new',
        'fecha_hora'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    /**
     * Registra un cambio en un hidrante.
     *
     * @param int $userId ID del usuario que realiza el cambio
     * @param int $hidranteId ID del hidrante modificado
     * @param string $campo Nombre del campo modificado
     * @param mixed $oldValue Valor anterior
     * @param mixed $newValue Nuevo valor
     * @return CambioEnHidrante
     */
    public static function registrarCambio($userId, $hidranteId, $campo, $oldValue, $newValue)
    {
        // Convertir valores null a string vacío para evitar problemas
        $oldValue = is_null($oldValue) ? '' : (string)$oldValue;
        $newValue = is_null($newValue) ? '' : (string)$newValue;
        
        // Solo registrar si realmente hubo un cambio
        if ($oldValue === $newValue) {
            return null;
        }

        // Ajustar la hora para compensar la diferencia de 6 horas
        $fechaHoraAjustada = Carbon::now()->subHours(6);

        return self::create([
            'id_user' => $userId,
            'id_hidrante' => $hidranteId,
            'campo' => $campo,
            'old' => $oldValue,
            'new' => $newValue,
            'fecha_hora' => $fechaHoraAjustada
        ]);
    }

    /**
     * Obtiene el usuario que realizó el cambio.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Obtiene el hidrante asociado al cambio.
     */
    public function hidrante()
    {
        return $this->belongsTo(Hidrante::class, 'id_hidrante');
    }
}