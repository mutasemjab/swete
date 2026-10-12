<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SplitUnitItem extends Model
{
    protected $fillable = [
        'split_unit_id',
        'material_id',
        'quantity',
        'unit_price',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_price' => 'decimal:3',
        'total'      => 'decimal:3',
        'notes'      => 'array',
    ];

    public function splitUnit(): BelongsTo
    {
        return $this->belongsTo(SplitUnit::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
