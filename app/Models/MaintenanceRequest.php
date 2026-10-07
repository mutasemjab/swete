<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** A customer-submitted request for a maintenance visit — awaits a Maintenance Manager's decision. */
class MaintenanceRequest extends Model
{
    use LogsActivity;

    protected $fillable = [
        'customer_id',
        'description',
        'preferred_date',
        'status',
        'decided_by',
        'decided_at',
        'decision_note',
        'linked_visit_id',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'decided_at'     => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function linkedVisit(): BelongsTo
    {
        return $this->belongsTo(MaintenanceVisit::class, 'linked_visit_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /** Approved requests for a customer that haven't yet been picked up by a visit. */
    public function scopeApprovedAvailableFor(Builder $query, int $customerId): Builder
    {
        return $query->where('customer_id', $customerId)
            ->where('status', 'approved')
            ->whereNull('linked_visit_id');
    }

    public function approve(User $manager): void
    {
        $this->update([
            'status'     => 'approved',
            'decided_by' => $manager->id,
            'decided_at' => now(),
        ]);
    }

    public function reject(User $manager, ?string $note): void
    {
        $this->update([
            'status'        => 'rejected',
            'decided_by'    => $manager->id,
            'decided_at'    => now(),
            'decision_note' => $note,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
