<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A manufacturing-stage attachment — a plain link, not an uploaded file. */
class PurchaseRequestAttachment extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'url',
        'label',
        'created_by',
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
