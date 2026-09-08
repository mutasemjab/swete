<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One approver's decision on one purchase request — snapshotted from PurchaseRequestApprover at creation time. */
class PurchaseRequestApproval extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'user_id',
        'decision',
        'decided_at',
        'note',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
