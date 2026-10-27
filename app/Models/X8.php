<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** The "X8" page's header — one per Price Analysis. Its line-by-line data lives in X8Item. */
class X8 extends Model
{
    protected $table = 'x8';

    protected $fillable = [
        'price_analysis_id',
        'created_by',
    ];

    public function priceAnalysis(): BelongsTo
    {
        return $this->belongsTo(PriceAnalysis::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(X8Item::class, 'x8_id');
    }
}
