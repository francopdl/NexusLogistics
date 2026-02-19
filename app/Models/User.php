<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Atributos que pueden asignarse masivamente
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
    ];

    // Atributos ocultos en serialización
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversiones de atributos
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Empresa del usuario
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Roles del usuario
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // Verificar si tiene un rol específico
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Verificar si tiene alguno de estos roles
    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('name', (array) $roles)->exists();
    }

    // Verificar si tiene todos estos roles
    public function hasAllRoles($roles)
    {
        return collect($roles)->every(fn($role) => $this->hasRole($role));
    }
}
