<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PriceAnalysis extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'branch_id',
        'tax_rate',
        'jd_rate',
        'with_tax',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'jd_rate'  => 'decimal:4',
        'with_tax' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceAnalysisItem::class);
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PA-%s-%05d', $year, $count);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->to_jd + $item->shipping);
    }

    /** The subtotal with tax added — equals the plain subtotal when `with_tax` is off, so callers never need to check the flag themselves. */
    public function getTotalWithTaxAttribute(): float
    {
        if (! $this->with_tax) {
            return $this->subtotal;
        }

        return round($this->subtotal * (1 + (float) $this->tax_rate / 100), 3);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
