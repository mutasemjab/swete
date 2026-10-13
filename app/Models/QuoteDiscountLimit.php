<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A singleton settings record — see QuoteDiscountLimitController, always exactly one row. */
class QuoteDiscountLimit extends Model
{
    protected $fillable = [
        'max_discount_percent',
        'max_discount_amount',
        'updated_by',
    ];

    protected $casts = [
        'max_discount_percent' => 'decimal:2',
        'max_discount_amount'  => 'decimal:3',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** The current limits, or an unsaved "no limit" default if nothing has been configured yet. */
    public static function current(): self
    {
        return static::first() ?? new static();
    }
}
