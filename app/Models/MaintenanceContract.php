<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MaintenanceContract extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'customer_id',
        'path',
        'signed_date',
        'expiry_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'signed_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MaintenanceContractPayment::class, 'contract_id');
    }

    public function scheduledVisits(): HasMany
    {
        return $this->hasMany(MaintenanceContractScheduledVisit::class, 'contract_id');
    }

    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('MC-%s-%05d', $year, $count);
    }

    /** Within a month of expiring (and not already past) — what the contracts-index highlight flags. */
    public function isExpiringSoon(): bool
    {
        $today = now()->startOfDay();

        return $this->expiry_date->greaterThanOrEqualTo($today)
            && $this->expiry_date->lessThanOrEqualTo($today->copy()->addMonth());
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->lessThan(now()->startOfDay());
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
