<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A manufacturing-stage attachment — a real uploaded file, stored on Bunny. */
class PurchaseRequestAttachment extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'path',
        'original_name',
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

    public function getUrlAttribute(): ?string
    {
        return FileStorage::url($this->path);
    }
}
