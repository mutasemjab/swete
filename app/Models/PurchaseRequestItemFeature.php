<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** An unstructured, free-text note attached to one purchase-request line item. */
class PurchaseRequestItemFeature extends Model
{
    protected $fillable = [
        'purchase_request_item_id',
        'value',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestItem::class, 'purchase_request_item_id');
    }
}
