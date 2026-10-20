<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A "سند قبض" (receipt voucher) — money actually received against an invoice. */
class InvoiceReceipt extends Model
{
    protected $fillable = [
        'number',
        'invoice_id',
        'date',
        'amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:3',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('RV-%s-%05d', $year, $count);
    }
}
