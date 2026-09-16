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

    /** Fixed set of work-scope line items the print's exclusion sentence draws from — not admin-manageable, unlike supply-scope/delivery-term. */
    public const WORK_SCOPE_ITEMS = [
        'pipe_work'           => ['ar' => 'أعمال المواسير', 'en' => 'Pipe Work'],
        'duct_work'           => ['ar' => 'أعمال الدكت', 'en' => 'Duct Work'],
        'electrical_works'    => ['ar' => 'الأعمال الكهربائية', 'en' => 'Electrical Works'],
        'power_control_cable' => ['ar' => 'كابلات الطاقة والتحكم', 'en' => 'Power and Control Cable'],
        'drain_works'         => ['ar' => 'أعمال الصرف', 'en' => 'Drain Works'],
        'civil_works'         => ['ar' => 'الأعمال المدنية', 'en' => 'Civil Works'],
    ];

    protected $fillable = [
        'number',
        'tender_id',
        'customer_id',
        'date',
        'status',
        'subtotal',
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
        'included_work_scopes',
        'additional_terms',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date'                  => 'date',
        'subtotal'              => 'decimal:3',
        'total'                 => 'decimal:3',
        'winching_included'     => 'boolean',
        'sales_tax_included'    => 'boolean',
        'customs_fees_included' => 'boolean',
        'include_boiler_note'   => 'boolean',
        'included_work_scopes'  => 'array',
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

        $lines = array_merge($lines, $this->workScopeNoteLines());

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

    /** The 6 fixed work-scope items bucket into "included" and "excluded" — both lists are stated explicitly, mirroring the tax/customs lines. */
    private function workScopeNoteLines(): array
    {
        $includedKeys = $this->included_work_scopes ?? [];
        $locale       = app()->isLocale('en') ? 'en' : 'ar';

        $included = [];
        $excluded = [];

        foreach (self::WORK_SCOPE_ITEMS as $key => $labels) {
            if (in_array($key, $includedKeys, true)) {
                $included[] = $labels[$locale];
            } else {
                $excluded[] = $labels[$locale];
            }
        }

        $lines = [];

        if ($included) {
            $lines[] = __('tenders.quote_note_prices_include', ['items' => implode(', ', $included)]);
        }

        if ($excluded) {
            $lines[] = __('tenders.quote_note_prices_exclude', ['items' => implode(', ', $excluded)]);
        }

        return $lines;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
