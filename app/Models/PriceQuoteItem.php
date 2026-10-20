<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceQuoteItem extends Model
{
    protected $fillable = [
        'price_quote_id',
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

    public function priceQuote(): BelongsTo
    {
        return $this->belongsTo(PriceQuote::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /** Usually at most one row (one per project this item was ever assigned under) — see ProjectItemAssignment. */
    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectItemAssignment::class, 'price_quote_item_id');
    }
}
