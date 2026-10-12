<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SplitUnit extends Model
{
    use LogsActivity;

    /** Fixed set of work-scope line items the print's exclusion sentence draws from. */
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
        'included_work_scopes',
        'additional_terms',
        'notes',
        'assigned_to',
        'invoice_id',
        'created_by',
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
        'included_work_scopes'  => 'array',
    ];

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
        return $this->hasMany(SplitUnitItem::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopePendingInvoice(Builder $query): Builder
    {
        return $query->whereNull('invoice_id');
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('SU-%s-%05d', $year, $count);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->get()->sum(fn ($item) => $item->quantity * $item->unit_price);
        $discount = static::resolveDiscount($subtotal, $this->discount_type, (float) $this->discount_value);

        $this->update([
            'subtotal'        => $subtotal,
            'discount_amount' => $discount,
            'total'           => $subtotal - $discount,
        ]);
    }

    public static function resolveDiscount(float $subtotal, ?string $type, float $value): float
    {
        $amount = $type === 'percent' ? $subtotal * $value / 100 : $value;

        return round(min(max($amount, 0), $subtotal), 3);
    }

    public function getVeryImportantNotesAttribute(): array
    {
        $lines = [];

        if ($this->validity_weeks) {
            $lines[] = __('split_units.quote_note_validity', ['weeks' => $this->validity_weeks]);
        }

        $lines[] = __('split_units.quote_note_technical_submittal');

        if ($this->supplyScope) {
            $lines[] = __('split_units.quote_note_supply_scope', ['scope' => $this->supplyScope->localized_name]);
        }

        $lines[] = __($this->winching_included ? 'split_units.quote_note_winching_included' : 'split_units.quote_note_winching_excluded');

        if ($this->deliveryTerm) {
            $lines[] = __('split_units.quote_note_delivery_term', ['term' => $this->deliveryTerm->localized_name]);
        }

        $lines = array_merge($lines, $this->taxCustomsNoteLines());

        if ($this->include_boiler_note) {
            $lines[] = __('split_units.quote_note_boiler');
        }

        $lines = array_merge($lines, $this->workScopeNoteLines());

        if ($this->currency) {
            $lines[] = __('split_units.quote_note_currency', ['code' => $this->currency->code]);
        }

        return array_merge($lines, $this->additionalTermsLines());
    }

    private function additionalTermsLines(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->additional_terms))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function taxCustomsNoteLines(): array
    {
        $included = [];
        $excluded = [];

        $this->sales_tax_included
            ? $included[] = __('split_units.quote_item_sales_tax')
            : $excluded[] = __('split_units.quote_item_sales_tax');

        $this->customs_fees_included
            ? $included[] = __('split_units.quote_item_customs_fees')
            : $excluded[] = __('split_units.quote_item_customs_fees');

        $lines = [];

        if ($included) {
            $lines[] = __('split_units.quote_note_prices_include', ['items' => implode(' ' . __('split_units.and') . ' ', $included)]);
        }

        if ($excluded) {
            $lines[] = __('split_units.quote_note_prices_exclude', ['items' => implode(' ' . __('split_units.and') . ' ', $excluded)]);
        }

        return $lines;
    }

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
            $lines[] = __('split_units.quote_note_prices_include', ['items' => implode(', ', $included)]);
        }

        if ($excluded) {
            $lines[] = __('split_units.quote_note_prices_exclude', ['items' => implode(', ', $excluded)]);
        }

        return $lines;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
