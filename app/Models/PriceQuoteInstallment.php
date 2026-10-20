<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceQuoteInstallment extends Model
{
    protected $fillable = [
        'price_quote_id',
        'description',
        'percentage',
        'sort_order',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];

    public function priceQuote(): BelongsTo
    {
        return $this->belongsTo(PriceQuote::class);
    }
}
