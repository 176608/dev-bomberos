<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hidrante extends Model
{
    public $timestamps = true; // Deshabilita/Habilita created_at y updated_at
    
    protected $table = 'hidrantes';
    protected $fillable = [
        'fecha_inspeccion',
        'fecha_tentativa',
        'numero_estacion',
        'numero_hidrante',
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
}

/*

Field                   Type            Null    Default     Extra               Key
----------------------- ----------------- ------- ----------- ------------------ ----
id                      - int(11)       - NO    - NULL      - auto_increment    - PRI
fecha_inspeccion        - date          - NO    - NULL
fecha_tentativa         - date          - NO    - NULL
numero_estacion         - int(4)        - NO    - NULL
numero_hidrante         - int(11)       - YES   - NULL
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