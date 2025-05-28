<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Colonias extends Model
{
    protected $table = 'colonias';
    
    protected $primaryKey = 'IDKEY';

    protected $fillable = [
        'NOMBRE',
        'TIPO'
    ];

    public $timestamps = false; // If the table doesn't have created_at and updated_at columns
}

/*

Field           Type            Null    Default   Extra
PRIMARYKEY      - int(4)        - YES   - NULL
SHAPE           - varchar(16)   - YES   - NULL
ID_COLO         - int(4)        - YES   - NULL
NOMBRE          - varchar(53)   - YES   - NULL  Usamos esta*
ETIQUETA        - varchar(38)   - YES   - NULL
TIPO            - varchar(21)   - YES   - NULL  Podemos usar esta*
FECHAUBICAIMIP  - varchar(10)   - YES   - NULL
YEARUBICAIMIP   - varchar(4)    - YES   - NULL
OBSERVACION     - varchar(128)  - YES   - NULL
cve_asen        - int(4)        - YES   - NULL
cve_ent         - varchar(2)    - YES   - NULL
cve_mun         - varchar(3)    - YES   - NULL
cve_loc         - varchar(4)    - YES   - NULL
cve_period      - int(1)        - YES   - NULL
cve_tipo        - int(2)        - YES   - NULL
IDKEY           - int(11)       - NO    - NULL  - auto_increment, PRIMARY KEY*

*/