<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'branch_id',
        'currency_id',
        'validity_weeks',
        'supply_scope_id',
        'delivery_term_id',
        'winching_included',
        'sales_tax_included',
        'customs_fees_included',
        'include_boiler_note',
        'additional_terms',
        'notes',
        'assigned_to',
        'invoice_id',
        'created_by',
        'is_current',
        'version',
        'root_id',
    ];

    protected $casts = [
        'date'                  => 'date',
        'subtotal'              => 'decimal:3',
        'total'                 => 'decimal:3',
        'discount_value'        => 'decimal:3',
        'discount_amount'       => 'decimal:3',
        'winching_included'     => 'boolean',
        'sales_tax_included'    => 'boolean',
        'customs_fees_included' => 'boolean',
        'include_boiler_note'   => 'boolean',
        'is_current'            => 'boolean',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function supplyScope(): BelongsTo
    {
        return $this->belongsTo(QuoteSupplyScope::class, 'supply_scope_id');
    }

    public function deliveryTerm(): BelongsTo
    {
        return $this->belongsTo(QuoteDeliveryTerm::class, 'delivery_term_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceQuoteItem::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PriceQuoteInstallment::class)->orderBy('sort_order');
    }

    /** Only meaningful on a frozen/archived row — the still-editable live quote it was cloned from. */
    public function root(): BelongsTo
    {
        return $this->belongsTo(self::class, 'root_id');
    }

    /** Only meaningful on the live (is_current=true) row — its frozen edit history, newest first. */
    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'root_id')->orderByDesc('version');
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    /** Assigned but not yet turned into an invoice — what the assignee's reminder counts. */
    public function scopePendingInvoice(Builder $query): Builder
    {
        return $query->whereNull('invoice_id');
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
        $discount = static::resolveDiscount($subtotal, $this->discount_type, (float) $this->discount_value);

        $this->update([
            'subtotal'        => $subtotal,
            'discount_amount' => $discount,
            'total'           => $subtotal - $discount,
        ]);
    }

    /** The amount actually deducted: a fixed value, or a percentage of the subtotal — never more than the subtotal. */
    public static function resolveDiscount(float $subtotal, ?string $type, float $value): float
    {
        $amount = $type === 'percent' ? $subtotal * $value / 100 : $value;

        return round(min(max($amount, 0), $subtotal), 3);
    }

    /**
     * Freezes the current DB state (header + items + installments) into a new, read-only row and
     * returns it — called by the controller right before applying an edit, so nothing is ever
     * silently overwritten. The live row (this one) keeps its id/number; only the frozen clone
     * gets a "-vN" number suffix (the real `number` column is unique, so two rows can't share it).
     */
    public function archiveCurrentVersion(): self
    {
        $this->loadMissing(['items', 'installments']);

        $archive = $this->replicate(['root_id']);
        $archive->number     = "{$this->number}-v{$this->version}";
        $archive->is_current = false;
        $archive->version    = $this->version;
        $archive->root_id    = $this->id;
        $archive->save();

        foreach ($this->items as $item) {
            $archive->items()->create($item->toArray());
        }

        foreach ($this->installments as $installment) {
            $archive->installments()->create($installment->toArray());
        }

        return $archive;
    }

    /**
     * The "Very Important Notes" clause block for the print document, in a fixed order —
     * each line skipped/worded per the per-quote toggles/selections. See the plan file's
     * Models section for the full rationale behind each step.
     */
    public function getVeryImportantNotesAttribute(): array
    {
        $lines = [];

        if ($this->validity_weeks) {
            $lines[] = __('tenders.quote_note_validity', ['weeks' => $this->validity_weeks]);
        }

        $lines[] = __('tenders.quote_note_technical_submittal');

        if ($this->supplyScope) {
            $lines[] = __('tenders.quote_note_supply_scope', ['scope' => $this->supplyScope->localized_name]);
        }

        $lines[] = __($this->winching_included ? 'tenders.quote_note_winching_included' : 'tenders.quote_note_winching_excluded');

        if ($this->deliveryTerm) {
            $lines[] = __('tenders.quote_note_delivery_term', ['term' => $this->deliveryTerm->localized_name]);
        }

        $lines = array_merge($lines, $this->taxCustomsNoteLines());

        if ($this->include_boiler_note) {
            $lines[] = __('tenders.quote_note_boiler');
        }

        $lines[] = __('tenders.quote_note_items_only');

        if ($this->currency) {
            $lines[] = __('tenders.quote_note_currency', ['code' => $this->currency->code]);
        }

        return array_merge($lines, $this->additionalTermsLines());
    }

    /** Free-text custom points typed by the user, one per line — appended after the auto-generated lines above. */
    private function additionalTermsLines(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->additional_terms))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /** Sales tax / customs fees bucket into "included" and "excluded" — a mixed state prints as two lines, a uniform state as one. */
    private function taxCustomsNoteLines(): array
    {
        $included = [];
        $excluded = [];

        $this->sales_tax_included
            ? $included[] = __('tenders.quote_item_sales_tax')
            : $excluded[] = __('tenders.quote_item_sales_tax');

        $this->customs_fees_included
            ? $included[] = __('tenders.quote_item_customs_fees')
            : $excluded[] = __('tenders.quote_item_customs_fees');

        $lines = [];

        if ($included) {
            $lines[] = __('tenders.quote_note_prices_include', ['items' => implode(' ' . __('tenders.and') . ' ', $included)]);
        }

        if ($excluded) {
            $lines[] = __('tenders.quote_note_prices_exclude', ['items' => implode(' ' . __('tenders.and') . ' ', $excluded)]);
        }

        return $lines;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
