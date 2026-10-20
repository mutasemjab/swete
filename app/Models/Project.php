<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'tender_id',
        'customer_id',
        'title',
        'title_en',
        'status',
        'notes',
        'manager_id',
        'engineer_id',
        'foreman_id',
        'review_reason',
        'created_by',
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

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function foreman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'foreman_id');
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    /** Every line item across all of the linked tender's CURRENT price quotes — a project has no price quote of its own. */
    public function quoteItems()
    {
        return $this->tender
            ?->priceQuotes()
            ->where('is_current', true)
            ->with(['items.material', 'items.assignments.assignee'])
            ->get()
            ->flatMap(fn ($quote) => $quote->items->map(function ($item) use ($quote) {
                $item->setAttribute('quoteNumber', $quote->number);

                return $item;
            }))
            ?? collect();
    }

    /** Project value (all current quotes' totals) vs. what's actually been received against their invoices. */
    public function financialSummary(): array
    {
        $quotes = $this->tender
            ?->priceQuotes()
            ->where('is_current', true)
            ->with('invoice.receipts')
            ->get()
            ?? collect();

        $value = (float) $quotes->sum('total');
        $paid  = (float) $quotes->sum(fn ($quote) => $quote->invoice?->paid_amount ?? 0);

        return [
            'value' => $value,
            'paid'  => $paid,
            'due'   => max($value - $paid, 0),
        ];
    }

    public function getLocalizedTitleAttribute(): string
    {
        return app()->isLocale('en') && $this->title_en
            ? $this->title_en
            : $this->title;
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PRJ-%s-%05d', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
