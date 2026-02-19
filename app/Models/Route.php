<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $fillable = [
        'fleet_id',
        'origin',
        'destination',
        'origin_latitude',
        'origin_longitude',
        'destination_latitude',
        'destination_longitude',
        'distance_km',
        'duration_seconds',
        'estimated_departure',
        'estimated_arrival',
        'status',
    ];

    protected $casts = [
        'estimated_departure' => 'datetime',
        'estimated_arrival' => 'datetime',
    ];

    // Flota que posee la ruta
    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }

    // Entregas en esta ruta
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
