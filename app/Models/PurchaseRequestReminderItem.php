<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestReminderItem extends Model
{
    protected $fillable = [
        'purchase_request_reminder_id',
        'material_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestReminder::class, 'purchase_request_reminder_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
