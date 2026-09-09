<?php

namespace App\Models;

use App\Models\Concerns\FormatsAddressLines;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use LogsActivity, FormatsAddressLines;

    protected $fillable = [
        'customer_group_id',
        'code',
        'name',
        'name_en',
        'phone',
        'email',
        'address',
        'tax_number',
        'opening_balance',
        'shipping_address_line1',
        'shipping_address_line1_en',
        'shipping_po_box',
        'shipping_postal_code',
        'shipping_city',
        'shipping_city_en',
        'shipping_country',
        'shipping_country_en',
        'status',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:3',
        'status'          => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'party_id')->where('party_type', 'customer');
    }

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class, 'party_id');
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    /** Stacked, locale-aware default shipping address — used to auto-fill a purchase request's shipping fields once a linked project/service call resolves this customer. */
    public function getLocalizedShippingAddressLinesAttribute(): array
    {
        return $this->addressLines(
            $this->shipping_address_line1, $this->shipping_address_line1_en,
            $this->shipping_po_box, $this->shipping_postal_code,
            $this->shipping_city, $this->shipping_city_en,
            $this->shipping_country, $this->shipping_country_en,
        );
    }

    public static function nextCode(): string
    {
        return sprintf('CUST-%05d', static::count() + 1);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
