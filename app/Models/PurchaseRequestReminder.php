<?php

namespace App\Models;

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
        'google_drive_url',
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

    /** The PR was created from this reminder — link them, and carry the drive link over as a real PR attachment. */
    public function markFulfilled(PurchaseRequest $purchaseRequest, User $user): void
    {
        $this->update([
            'status'              => 'fulfilled',
            'purchase_request_id' => $purchaseRequest->id,
            'fulfilled_by'        => $user->id,
            'fulfilled_at'        => now(),
        ]);

        $purchaseRequest->attachments()->create([
            'url'        => $this->google_drive_url,
            'label'      => __('tenders.reminder_drive_link_label'),
            'created_by' => $user->id,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
