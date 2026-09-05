<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'action',
        'status',
        'requested_by',
        'approver_id',
        'note',
        'decision_note',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Human-readable label for the approvable, e.g. "طلب مواد #12".
     * Uses the approvable's own approvalLabel() when it defines one,
     * otherwise falls back to its class basename + id.
     */
    public function getApprovableLabelAttribute(): string
    {
        $approvable = $this->approvable;

        if (! $approvable) {
            return "#{$this->approvable_id}";
        }

        if (method_exists($approvable, 'approvalLabel')) {
            return $approvable->approvalLabel();
        }

        return class_basename($approvable) . ' #' . $approvable->getKey();
    }
}
