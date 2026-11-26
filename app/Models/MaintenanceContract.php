<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MaintenanceContract extends Model
{
    use LogsActivity;

    public const STATUSES = ['draft', 'active', 'suspended', 'terminated'];

    protected $fillable = [
        'number',
        'customer_id',
        'path',
        'status',
        'signed_date',
        'expiry_date',
        'notes',
        'created_by',
        'emergency_visit_limit',
    ];

    protected $casts = [
        'signed_date' => 'date',
        'expiry_date' => 'date',
        'emergency_visit_limit' => 'integer',
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

    public function contractUnits(): HasMany
    {
        return $this->hasMany(MaintenanceContractUnit::class, 'contract_id');
    }

    /** Units currently covered by this contract (not soft-removed). */
    public function activeUnits()
    {
        return $this->contractUnits()->whereNull('removed_at')->with('unit');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(MaintenanceContractSchedule::class, 'contract_id');
    }

    public function emergencyVisits(): HasMany
    {
        return $this->hasMany(MaintenanceVisit::class, 'contract_id')->where('kind', 'emergency');
    }

    public function getEmergencyVisitsRemainingAttribute(): ?int
    {
        return $this->emergency_visit_limit === null ? null
            : max(0, $this->emergency_visit_limit - $this->emergencyVisits()->count());
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    /** Active and not past its own end date — the window auto-generation actually works within. */
    public function inForce(): bool
    {
        return $this->isActive() && ! $this->isExpired();
    }

    public function getUrlAttribute(): ?string
    {
        return FileStorage::url($this->path);
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
