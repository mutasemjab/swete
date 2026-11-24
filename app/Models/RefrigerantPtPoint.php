<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One (temperature, saturation pressure) point from a refrigerant's real P-T chart — entered by an engineer, never invented. */
class RefrigerantPtPoint extends Model
{
    protected $fillable = [
        'refrigerant',
        'temperature_c',
        'pressure_bar',
        'source',
        'created_by',
    ];

    protected $casts = [
        'temperature_c' => 'float',
        'pressure_bar'  => 'float',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
