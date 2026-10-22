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
        'is_current',
        'version',
        'root_id',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'version'    => 'integer',
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

    /** Only meaningful on a frozen/archived row — the still-live project it was split off from. */
    public function root(): BelongsTo
    {
        return $this->belongsTo(self::class, 'root_id');
    }

    /** Only meaningful on the live (is_current=true) row — its frozen split history, newest first. */
    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'root_id')->orderByDesc('version');
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

    /**
     * Every line item on the live origin quote, plus any still-PENDING Variation Order's quote —
     * assignable to employees from the moment a Variation Order is submitted, not only once
     * approved. An APPROVED Variation Order no longer needs merging here at all: approving one
     * splits the project (see archiveCurrentVersion()) instead, so the live row's own price_quote_id
     * already points straight at it afterward.
     */
    public function quoteItems()
    {
        $pendingVoQuoteIds = $this->variationOrders()->where('status', 'pending')->pluck('price_quote_id');
        $quoteIds = collect([$this->price_quote_id])->merge($pendingVoQuoteIds)->filter()->unique();

        return PriceQuoteItem::whereIn('price_quote_id', $quoteIds)
            ->with(['material', 'assignments.assignee', 'priceQuote'])
            ->get()
            ->map(function ($item) {
                $item->setAttribute('quoteNumber', $item->priceQuote->number);
                // null = this item belongs to the project's own live origin quote, not a pending Variation Order.
                $item->setAttribute('voStatus', $item->price_quote_id === $this->price_quote_id ? null : 'pending');

                return $item;
            });
    }

    /** Project value vs. what's actually been received against the live origin quote's invoice. */
    public function financialSummary(): array
    {
        $quote = $this->priceQuote?->load('invoice.receipts');

        $value = (float) ($quote?->total ?? 0);
        $paid  = (float) ($quote?->invoice?->paid_amount ?? 0);

        return [
            'value' => $value,
            'paid'  => $paid,
            'due'   => max($value - $paid, 0),
        ];
    }

    /** The stable "PRJ-YYYY-00001" part of the number, with its "-P{n}" revision suffix stripped off. */
    public function getBaseNumberAttribute(): string
    {
        return preg_replace('/-P\d+$/', '', $this->number);
    }

    /**
     * Freezes the current state (header only — no child rows to clone, unlike PriceQuote, since a
     * Project's "content" is just price_quote_id, a plain column) into a new, read-only row and
     * returns it. The live row (this one, same id throughout) moves onto its next "-P{n}" number
     * first, freeing up the old number for the frozen clone to take over — same ordering as
     * PriceQuote::archiveCurrentVersion(), needed because `number` is unique.
     */
    public function archiveCurrentVersion(): self
    {
        $oldNumber  = $this->number;
        $oldVersion = $this->version;

        $this->update([
            'number'  => $this->base_number . '-P' . $oldVersion,
            'version' => $oldVersion + 1,
        ]);

        $archive = $this->replicate(['root_id']);
        $archive->number     = $oldNumber;
        $archive->is_current = false;
        $archive->version    = $oldVersion;
        $archive->root_id    = $this->id;
        $archive->save();

        return $archive;
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

        return sprintf('PRJ-%s-%05d-P0', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
