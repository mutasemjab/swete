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
        'price_quote_id',
        'title',
        'title_en',
        'status',
        'notes',
        'manager_id',
        'engineer_id',
        'foreman_id',
        'review_reason',
        'variation_round',
        'created_by',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    /** The specific quote this project was converted from — see Tenders\TenderController@convertToProject. */
    public function priceQuote(): BelongsTo
    {
        return $this->belongsTo(PriceQuote::class);
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(ProjectVariationOrder::class);
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

    public function questions(): HasMany
    {
        return $this->hasMany(ProjectQuestion::class);
    }

    /** This project's own origin quote, plus any Variation Order quote that's been approved. */
    private function relevantQuoteIds()
    {
        return collect([$this->price_quote_id])
            ->merge($this->variationOrders()->where('status', 'approved')->pluck('price_quote_id'))
            ->filter()
            ->unique();
    }

    /**
     * Every line item across the origin quote + any Variation Order quote that isn't rejected —
     * a Variation Order's items are assignable to employees from the moment it's created, not only
     * once formally approved (approval still gates whether it counts toward financialSummary()).
     */
    public function quoteItems()
    {
        $variationOrders = $this->variationOrders()->where('status', '!=', 'rejected')->get();
        $voStatusByQuote = $variationOrders->pluck('status', 'price_quote_id');
        $quoteIds = collect([$this->price_quote_id])->merge($voStatusByQuote->keys())->filter()->unique();

        return PriceQuoteItem::whereIn('price_quote_id', $quoteIds)
            ->with(['material', 'assignments.assignee', 'priceQuote'])
            ->get()
            ->map(function ($item) use ($voStatusByQuote) {
                $item->setAttribute('quoteNumber', $item->priceQuote->number);
                // null = this item belongs to the project's own origin quote, not a Variation Order.
                $item->setAttribute('voStatus', $voStatusByQuote->get($item->price_quote_id));

                return $item;
            });
    }

    /** Project value (origin + approved VO quotes' totals) vs. what's actually been received against their invoices. */
    public function financialSummary(): array
    {
        $quotes = PriceQuote::whereIn('id', $this->relevantQuoteIds())->with('invoice.receipts')->get();

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
