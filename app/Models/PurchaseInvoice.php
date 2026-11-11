<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * A warehouse purchase invoice from a supplier — carries price, unlike the StockVoucher it
 * auto-creates (see PurchaseInvoiceController::store()), which only ever carries quantities.
 * Created standalone, or converted from a PurchaseOrder (its items carried over, never re-typed).
 */
class PurchaseInvoice extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'supplier_id',
        'supplier_invoice_number',
        'warehouse_id',
        'purchase_order_id',
        'stock_voucher_id',
        'date',
        'notes',
        'subtotal',
        'total',
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

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function stockVoucher(): BelongsTo
    {
        return $this->belongsTo(StockVoucher::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('PINV-%s-%05d', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
