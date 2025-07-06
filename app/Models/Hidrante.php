<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hidrante extends Model
{
    public $timestamps = true;
    
    protected $table = 'hidrantes';
    protected $fillable = [
        'stat',
        'fecha_inspeccion',
        'fecha_tentativa',
        'numero_estacion',
        'id_calle',
        'calle',
        'id_y_calle',
        'y_calle',
        'id_colonia',
        'colonia',
        'llave_hidrante',
        'presion_agua',
        'color',
        'llave_fosa',
        'ubicacion_fosa',
        'hidrante_conectado_tubo',
        'estado_hidrante',
        'marca',
        'anio',
        'observaciones',
        'oficial',
        'create_user_id',
        'update_user_id'
    ];

    protected $casts = [
        'fecha_inspeccion' => 'date',
        'fecha_tentativa' => 'date', 
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected function getFechaTentativaAttribute($value)
    {
        if (!$value || $value === '0000-00-00') {
            return null;
        }
        return \Carbon\Carbon::parse($value);
    }

    public function coloniaLocacion()
    {
        return $this->belongsTo(Colonias::class, 'id_colonia', 'IDKEY');
    }

    public function callePrincipal()
    {
        return $this->belongsTo(CatalogoCalle::class, 'id_calle', 'IDKEY');
    }

    public function calleSecundaria()
    {
        return $this->belongsTo(CatalogoCalle::class, 'id_y_calle', 'IDKEY');
    }

    // Add relationships for users
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'create_user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_user_id');
    }

    protected function setDefaultValues($attributes)
    {
        // Para campos sin definir
        foreach(['calle', 'y_calle', 'colonia'] as $field) {
            if (empty($attributes[$field])) {
                $attributes[$field] = 'Sin definir';
                $attributes['id_' . $field] = null;
            }
        }
        return $attributes;
    }

    protected function setPendingValues($attributes)
    {
        $pendingFields = [
            'calle' => ['field' => 'calle', 'id' => 'id_calle'],
            'y_calle' => ['field' => 'y_calle', 'id' => 'id_y_calle'],
            'colonia' => ['field' => 'colonia', 'id' => 'id_colonia']
        ];

        foreach($pendingFields as $config) {
            $attributes[$config['field']] = 'Pendiente';
            $attributes[$config['id']] = 0;
        }
        return $attributes;
    }

    public static function calcularStat($data)
    {
        $total = 15;
        $cumple = 0;

        // 1. fecha_inspeccion (cuenta si tiene una fecha válida)
        if (!empty($data['fecha_inspeccion']) && $data['fecha_inspeccion'] !== '0000-00-00') $cumple++;

        // 2. fecha_tentativa (cuenta si es diferente de "0000-00-00" y no es null)
        if (!empty($data['fecha_tentativa']) && $data['fecha_tentativa'] !== '0000-00-00') $cumple++;

        // 3. numero_estacion (cuenta si NO contiene 'S/I')
        if (!empty($data['numero_estacion']) && stripos($data['numero_estacion'], 'S/I') === false) $cumple++;

        // 4. calle (cuenta si es diferente de "Pendiente")
        if (!empty($data['calle']) && $data['calle'] !== 'Pendiente') $cumple++;

        // 5. id_calle (cuenta si es diferente de 0 y no null)
        if (!empty($data['id_calle']) && $data['id_calle'] != 0) $cumple++;

        // 6. llave_hidrante (cuenta si NO contiene 'S/I')
        if (!empty($data['llave_hidrante']) && stripos($data['llave_hidrante'], 'S/I') === false) $cumple++;

        // 7. presion_agua (cuenta si NO contiene 'S/I')
        if (!empty($data['presion_agua']) && stripos($data['presion_agua'], 'S/I') === false) $cumple++;

        // 8. color (cuenta si NO contiene 'S/I')
        if (!empty($data['color']) && stripos($data['color'], 'S/I') === false) $cumple++;

        // 9. llave_fosa (cuenta si NO contiene 'S/I')
        if (!empty($data['llave_fosa']) && stripos($data['llave_fosa'], 'S/I') === false) $cumple++;

        // 10. ubicacion_fosa (cuenta si NO contiene 'S/I')
        if (!empty($data['ubicacion_fosa']) && stripos($data['ubicacion_fosa'], 'S/I') === false) $cumple++;

        // 11. hidrante_conectado_tubo (cuenta si NO contiene 'S/I')
        if (!empty($data['hidrante_conectado_tubo']) && stripos($data['hidrante_conectado_tubo'], 'S/I') === false) $cumple++;

        // 12. estado_hidrante (cuenta si NO contiene 'S/I')
        if (!empty($data['estado_hidrante']) && stripos($data['estado_hidrante'], 'S/I') === false) $cumple++;

        // 13. marca (cuenta si no es nulo ni vacío)
        if (isset($data['marca']) && trim($data['marca']) !== '') $cumple++;

        // 14. anio (cuenta si no es null y distinto de 0 y distinto de "0")
        if (!empty($data['anio']) && $data['anio'] != 0 && $data['anio'] !== "0") $cumple++;

        // 15. oficial (cuenta si NO contiene 'S/I')
        if (!empty($data['oficial']) && stripos($data['oficial'], 'S/I') === false) $cumple++;

        // Calcula porcentaje y lo convierte a string de 3 dígitos
        $porcentaje = round(($cumple / $total) * 100);
        return str_pad($porcentaje, 3, '0', STR_PAD_LEFT);
    }
}

/*

Field                   Type            Null    Default     Extra               Key
----------------------- ----------------- ------- ----------- ------------------ ----
id                      - int(11)       - NO    - NULL      - auto_increment    - PRI
stat                    - varchar(4)    - YES   - NULL      *Informacion del estatus del hidrante en SISTEMA
fecha_inspeccion        - date          - NO    - NULL
fecha_tentativa         - date          - NO    - NULL
numero_estacion         - char(4)       - NO    - NULL
numero_hidrante AAjustar- int(11)       - YES   - NULL      BORRAR PRONTO************
calle                   - varchar(255)  - YES   - NULL
id_calle                - int(11)       - YES   - NULL
y_calle                 - varchar(255)  - YES   - NULL
id_y_calle              - int(11)       - YES   - NULL
colonia                 - varchar(255)  - YES   - NULL
id_colonia              - int(10)       - YES   - NULL
llave_hidrante          - varchar(255)  - YES   - NULL
presion_agua            - varchar(50)   - YES   - NULL
color                   - varchar(50)   - YES   - NULL
llave_fosa              - varchar(255)  - YES   - NULL
ubicacion_fosa          - varchar(255)  - YES   - NULL
hidrante_conectado_tubo - varchar(255)  - YES   - NULL
estado_hidrante         - varchar(255)  - YES   - NULL
marca                   - varchar(255)  - YES   - NULL
anio                    - int(11)       - YES   - NULL
observaciones           - text          - YES   - NULL
oficial                 - varchar(255)  - YES   - NULL
create_user_id          - int(4)        - NO    - NULL  *El id del usuario que crea el registro
update_user_id          - int(4)        - NO    - NULL  *El id del usuario que actualiza el registro
created_at              - datetime      - YES   - NULL  *Fecha de creación del registro
updated_at              - datetime      - YES   - NULL  *Fecha de actualización del registro
*/