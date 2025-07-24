<?php
/* <!-- Archivo Bomberos & SIGEM- NO ELIMINAR COMENTARIO --> */
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'log_in_status', // Añadir el nuevo campo
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'log_in_status' => 'integer',
        ];
    }
}


/* 
Field               Type                                      Null Key  Default Extra
id              -bigint(20) unsigned                          -NO -PRI  -NULL   -auto_increment
name                -varchar(255)                             -NO       -NULL
email               -varchar(255)                             -NO -UNI  -NULL
email_verified_at   -timestamp                                -YES      -NULL
password            -varchar(255)                             -NO       -NULL
remember_token      -varchar(100)                             -YES      -NULL
created_at          -timestamp                                -YES      -NULL
updated_at          -timestamp                                -YES      -NULL
role      -enum('Capturista','Desarrollador','Administrador')   -NO       -Capturista
status              -tinyint(1)                               -NO       -1
log_in_status
*/