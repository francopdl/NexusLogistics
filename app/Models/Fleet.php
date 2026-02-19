<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fleet extends Model
{
    protected $fillable = [
        'name',
        'company_id',
        'description',
    ];

    // Empresa de la flota
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Vehículos de la flota
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    // Rutas de la flota
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
}
