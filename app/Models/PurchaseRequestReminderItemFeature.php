<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** An unstructured, free-text note attached to one purchase-request-reminder line item. */
class PurchaseRequestReminderItemFeature extends Model
{
    protected $fillable = [
        'purchase_request_reminder_item_id',
        'value',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestReminderItem::class, 'purchase_request_reminder_item_id');
    }
}
