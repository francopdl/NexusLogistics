<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'fleet_id',
        'license_plate',
        'vehicle_type',
        'manufacturer',
        'model',
        'year',
        'status',
    ];

    // Flota del vehículo
    public function fleet(): BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }
}
