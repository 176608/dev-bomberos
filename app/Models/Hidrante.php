<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hidrante extends Model
{
    //public $timestamps = false; // Añade esta línea
    
    protected $table = 'hidrantes';
    protected $fillable = [
        'fecha_inspeccion',
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
        'update_user_id'
    ];

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
}

/*

Field                   Type            Null    Default     Extra               Key
----------------------- ----------------- ------- ----------- ------------------ ----
id                      - int(11)       - NO    - NULL      - auto_increment    - PRI
fecha_inspeccion        - date          - NO    - current_timestamp()    ------>>> fecha_alta -> fecha_inspeccion
numero_estacion         - int(4)        - NO    - NULL
numero_hidrante         - int(11)       - YES   - NULL
calle                   - varchar(255)  - YES   - NULL
id_calle                - int(11)       - NO    - NULL
y_calle                 - varchar(255)  - YES   - NULL
id_y_calle              - int(11)       - NO    - NULL
colonia                 - varchar(255)  - YES   - NULL
id_colonia              - int(10)       - NO    - NULL
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
update_user_id          - int(4)        - NO    - NULL por defecto el user que dio de alta
*/