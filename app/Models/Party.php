<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** Shared record for both customers (العملاء) and suppliers (الموردين). */
class Party extends Model
{
    use LogsActivity;

    protected $fillable = [
        'type',
        'party_group_id',
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

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(PartyGroup::class, 'party_group_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    public static function nextCode(string $type): string
    {
        $prefix = $type === 'customer' ? 'CUST' : 'SUPP';
        $count  = static::ofType($type)->count() + 1;

        return sprintf('%s-%05d', $prefix, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
