<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PriceQuote extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'tender_id',
        'customer_id',
        'date',
        'status',
        'subtotal',
        'total',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date'     => 'date',
        'subtotal' => 'decimal:3',
        'total'    => 'decimal:3',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceQuoteItem::class);
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PQ-%s-%05d', $year, $count);
    }

    public function recalculateTotals(): void
    {
        // Query fresh rather than trust a possibly stale cached `items` relation.
        $subtotal = $this->items()->get()->sum(fn ($item) => $item->quantity * $item->unit_price);

        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
