<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PurchaseOrder extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'supplier_id',
        'warehouse_id',
        'date',
        'notes',
        'subtotal',
        'total',
        'status',
        'purchase_invoice_id',
        'created_by',
    ];

    protected $casts = [
        'date'     => 'date',
        'subtotal' => 'decimal:3',
        'total'    => 'decimal:3',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Only meaningful once `status` is 'converted'. */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PO-%s-%05d', $year, $count);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->get()->sum(fn ($item) => $item->quantity * $item->unit_price);

        $this->update(['subtotal' => $subtotal, 'total' => $subtotal]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
