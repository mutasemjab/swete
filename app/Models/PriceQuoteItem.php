<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceQuoteItem extends Model
{
    protected $fillable = [
        'price_quote_id',
        'material_id',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_price' => 'decimal:3',
        'total'      => 'decimal:3',
    ];

    public function priceQuote(): BelongsTo
    {
        return $this->belongsTo(PriceQuote::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
