<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'route_id',
        'client_id',
        'package_info',
        'status',
        'latitude',
        'longitude',
    ];

    // Ruta de la entrega
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    // Cliente de la entrega
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
