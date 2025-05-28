<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Calles extends Model
{
    protected $table = 'catalogocalle';
    
    protected $primaryKey = 'IDKEY';

    protected $fillable = [
        'Nomvial',
        'Tipovial',
        'CLAVE'
    ];

    public $timestamps = false; // If the table doesn't have created_at and updated_at columns
}

/*

Field       Type            Null    Default   Extra
Cve_ent     -varchar(2)     - YES   - NULL
Cve_mun     -varchar(3)     - YES   - NULL
Cve_loc     -varchar(4)     - YES   - NULL
Cve_vial    -varchar(5)     - YES   - NULL
Tipovial    -varchar(20)    - YES   - NULL  Puede ser util*
Nomvial     -varchar(100)   - YES   - NULL  Uso principal*
CLAVE       -varchar(20)    - YES   - NULL
fecha       -varchar(10)    - YES   - NULL   NO TIENE NADA*
IDKEY       -int(11)        - NO    - NULL   - auto_increment, PRIMARY KEY

*/