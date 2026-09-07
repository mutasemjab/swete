<?php

namespace App\Models;

use App\Models\Concerns\FormatsAddressLines;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseRequest extends Model
{
    use LogsActivity, FormatsAddressLines;

    protected $fillable = [
        'number',
        'project_id',
        'service_call_id',
        'date',
        'supplier_id',
        'branch_id',
        'shipping_address_line1',
        'shipping_address_line1_en',
        'shipping_po_box',
        'shipping_postal_code',
        'shipping_city',
        'shipping_city_en',
        'shipping_country',
        'shipping_country_en',
        'location_scope',
        'governorate',
        'country_id',
        'currency_id',
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function serviceCall(): BelongsTo
    {
        return $this->belongsTo(ServiceCall::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    /** Stacked, locale-aware request address — derived live from the branch, not duplicated here. */
    public function getRequestAddressLinesAttribute(): array
    {
        return $this->branch?->localized_address_lines ?? [];
    }

    /** Stacked, locale-aware shipping address — where the goods actually go (may differ from the branch). */
    public function getShippingAddressLinesAttribute(): array
    {
        return $this->addressLines(
            $this->shipping_address_line1, $this->shipping_address_line1_en,
            $this->shipping_po_box, $this->shipping_postal_code,
            $this->shipping_city, $this->shipping_city_en,
            $this->shipping_country, $this->shipping_country_en,
        );
    }

    public function getLocalizedGovernorateAttribute(): ?string
    {
        if (! $this->governorate || ! isset(Tender::JORDAN_GOVERNORATES[$this->governorate])) {
            return null;
        }

        return Tender::JORDAN_GOVERNORATES[$this->governorate][app()->isLocale('en') ? 'en' : 'ar'];
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PR-%s-%05d', $year, $count);
    }

    public function recalculateTotals(): void
    {
        // Query fresh rather than trust a possibly stale cached `items` relation
        // (e.g. right after syncing a new set of items on an already-loaded model).
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
