<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One row of the "Additional Notes" print block — free-text {label, value}, not an enum. */
class PurchaseRequestNote extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'label',
        'value',
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
}
