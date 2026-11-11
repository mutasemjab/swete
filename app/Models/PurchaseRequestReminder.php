<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** An employee's request that a Purchase Request be raised — not the PR itself, just a heads-up with project/materials/quantities for whoever picks it up. */
class PurchaseRequestReminder extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_id',
        'status',
        'purchase_request_id',
        'requested_by',
        'fulfilled_by',
        'fulfilled_at',
    ];

    protected $casts = [
        'fulfilled_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function fulfiller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestReminderItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PurchaseRequestReminderAttachment::class);
    }

    /** Locked once a purchase request has been created from it — the request is considered finalized at that point. */
    public function isEditable(): bool
    {
        return $this->status !== 'fulfilled';
    }

    /** The PR was created from this reminder — link them, and copy this reminder's own
     *  attachments onto the new PR too (each gets its own independent stored file, so deleting
     *  one later never affects the other). */
    public function markFulfilled(PurchaseRequest $purchaseRequest, User $user): void
    {
        $this->update([
            'status'              => 'fulfilled',
            'purchase_request_id' => $purchaseRequest->id,
            'fulfilled_by'        => $user->id,
            'fulfilled_at'        => now(),
        ]);

        foreach ($this->attachments as $attachment) {
            $purchaseRequest->attachments()->create([
                'path'          => FileStorage::copy($attachment->path, 'purchase-requests'),
                'original_name' => $attachment->original_name,
                'label'         => __('tenders.reminder_attachment_label'),
                'created_by'    => $user->id,
            ]);
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
