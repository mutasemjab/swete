<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use LogsActivity;

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

    public static function nextCode(): string
    {
        return sprintf('CUST-%05d', static::count() + 1);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
