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
        'opening_balance',
        'status',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:3',
        'status'          => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SupplierGroup::class, 'supplier_group_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'party_id')->where('party_type', 'supplier');
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
