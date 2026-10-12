<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceQuoteInstallment extends Model
{
    protected $fillable = [
        'price_quote_id',
        'description',
        'amount',
        'due_date',
        'sort_order',
    ];

    protected $casts = [
        'amount'   => 'decimal:3',
        'due_date' => 'date',
    ];

    public function priceQuote(): BelongsTo
    {
        return $this->belongsTo(PriceQuote::class);
    }
}
