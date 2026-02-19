<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'company_id',
    ];

    // Empresa del cliente
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Entregas del cliente
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
