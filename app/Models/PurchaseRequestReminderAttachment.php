<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestReminderAttachment extends Model
{
    protected $fillable = [
        'purchase_request_reminder_id',
        'path',
        'original_name',
        'created_by',
    ];

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestReminder::class, 'purchase_request_reminder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUrlAttribute(): ?string
    {
        return FileStorage::url($this->path);
    }
}
