<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use LogsActivity;

    protected $fillable = [
        'supplier_group_id',
        'code',
        'name',
        'name_en',
        'phone',
        'email',
        'address',
        'tax_number',
        'commercial_registration_path',
        'personal_id_path',
        'opening_balance',
        'location_scope',
        'governorate_id',
        'country_id',
        'shipping_instruction',
        'status',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:3',
        'status'          => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SupplierGroup::class, 'supplier_group_id');
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'party_id')->where('party_type', 'supplier');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContact::class)->orderBy('sort_order');
    }

    public function extraFieldValues(): HasMany
    {
        return $this->hasMany(ClientExtraFieldValue::class, 'entity_id')
            ->where('entity_type', 'supplier');
    }

    public function getCommercialRegistrationUrlAttribute(): ?string
    {
        return $this->commercial_registration_path ? asset($this->commercial_registration_path) : null;
    }

    public function getPersonalIdUrlAttribute(): ?string
    {
        return $this->personal_id_path ? asset($this->personal_id_path) : null;
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    public static function nextCode(): string
    {
        return sprintf('SUPP-%05d', static::count() + 1);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
