<?php
/* <!-- Archivo SGU - NO ELIMINAR COMENTARIO --> */
namespace App\Models\SGU;

use App\Models\SGU\Traits\AuditableUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, AuditableUsuario;

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
        'log_in_status',
        'initial_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'initial_token',
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

    /**
     * Verificar si el usuario tiene un rol específico
     * Compatible con los valores enum: 'Capturista', 'Desarrollador', 'Administrador'
     */
    public function hasRole($role)
    {
        // Primero comparar exacto (case-sensitive)
        if ($this->role === $role) {
            return true;
        }
        
        // Si no coincide exacto, hacer comparación case-insensitive
        $userRole = strtolower($this->role);
        $checkRole = strtolower($role);
        
        // Mapear variaciones comunes
        $roleMap = [
            'admin' => 'administrador',
            'administrador' => 'administrador',
            'capturista' => 'capturista',
            'desarrollador' => 'desarrollador',
            'dev' => 'desarrollador',
        ];
        
        $normalizedCheckRole = $roleMap[$checkRole] ?? $checkRole;
        $normalizedUserRole = $roleMap[$userRole] ?? $userRole;
        
        return $normalizedUserRole === $normalizedCheckRole;
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive()
    {
        return $this->status == 1;
    }
}
