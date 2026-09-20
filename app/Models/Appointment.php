<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Appointment extends Model
{
    use LogsActivity;

    public const STATUSES = ['scheduled', 'completed'];

    protected $fillable = [
        'title',
        'appointment_date',
        'appointment_type_id',
        'customer_id',
        'assigned_to',
        'status',
        'completed_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'completed_at'     => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    /** Still open and its date is today or already passed — what the reminder badge counts. */
    public function scopeDue(Builder $query): Builder
    {
        return $query->open()->whereDate('appointment_date', '<=', today());
    }

    public function isDueToday(): bool
    {
        return $this->status === 'scheduled' && $this->appointment_date->isToday();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'scheduled' && $this->appointment_date->lt(today());
    }

    public function markCompleted(): void
    {
        $this->update(['status' => 'completed', 'completed_at' => now()]);
    }

    public function reopen(): void
    {
        $this->update(['status' => 'scheduled', 'completed_at' => null]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
