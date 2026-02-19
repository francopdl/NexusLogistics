<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
    ];

    // Usuarios de la empresa
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Clientes de la empresa
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    // Flotas de la empresa
    public function fleets(): HasMany
    {
        return $this->hasMany(Fleet::class);
    }
}
