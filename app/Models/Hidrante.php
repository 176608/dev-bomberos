<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Hidrante extends Model
{
    protected $table = 'hidrantes';
    protected $fillable = [
        'fecha_alta',
        'alta_user_id',
        'update_user_id',
        'numero_estacion',
        'numero_hidrante',
        'calle',
        'id_calle',
        'y_calle',
        'id_y_calle',
        'colonia',
        'id_colonia',
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
        'oficial'
    ];

    public function colonia()
    {
        return $this->belongsTo(Colonias::class, 'id_colonia', 'IDKEY');
    }

    public function calle()
    {
        return $this->belongsTo(Calles::class, 'id_calle', 'IDKEY');
    }

    public function yCalle()
    {
        return $this->belongsTo(Calles::class, 'id_y_calle', 'IDKEY');
    }
}

/*

Field                   Type            Null    Default     Extra               Key
----------------------- ----------------- ------- ----------- ------------------ ----
id                      - int(11)       - NO    - NULL      - auto_increment    - PRI
fecha_alta              - date          - NO    - current_timestamp()
alta_user_id            - int(4)        - NO    - NULL
update_user_id          - int(4)        - NO    - NULL
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
*/