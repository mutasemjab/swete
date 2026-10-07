<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceContractScheduledVisit extends Model
{
    public const TYPES = ['emergency', 'periodic'];

    protected $fillable = [
        'contract_id',
        'scheduled_date',
        'type',
        'notes',
        'notified_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'notified_at'    => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(MaintenanceContract::class, 'contract_id');
    }

    /** Due today or already past — what the contract's visits list highlights. */
    public function scopeDue(Builder $query): Builder
    {
        return $query->whereDate('scheduled_date', '<=', today());
    }

    /** Due (today-or-past) and not yet emailed — the once-only reminder guard. */
    public function scopeNeedingNotification(Builder $query): Builder
    {
        return $query->due()->whereNull('notified_at');
    }

    public function isDueToday(): bool
    {
        return $this->scheduled_date->isToday();
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_date->lt(today());
    }
}
