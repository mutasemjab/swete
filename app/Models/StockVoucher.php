<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Shared header for the 4 stock-movement voucher types: receipt, issue, transfer, stocktake.
 * `warehouse_id` is the only warehouse involved for receipt/issue/stocktake; for transfer it's
 * the source and `destination_warehouse_id` is the target. `customer_id` is only ever set on an
 * `issue` voucher issued directly to a customer — nullable, since `MaterialRequest::fulfill()`
 * also auto-creates plain internal `issue` vouchers with no customer at all.
 */
class StockVoucher extends Model
{
    use LogsActivity;

    protected $fillable = [
        'type',
        'number',
        'warehouse_id',
        'destination_warehouse_id',
        'customer_id',
        'date',
        'reference_no',
        'notes',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'date'      => 'date',
        'posted_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockVoucherItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public static function nextNumber(string $type): string
    {
        $prefix = match ($type) {
            'receipt'   => 'REC',
            'issue'     => 'ISS',
            'transfer'  => 'TRF',
            'stocktake' => 'STK',
        };

        $year  = now()->format('Y');
        $count = static::where('type', $type)->whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }

    /**
     * Post a draft voucher: writes ledger movements and updates live balances.
     * Issue/transfer are checked for sufficient stock first — stocktake never is, since counting
     * down to a lower quantity than the system has is definitionally always valid. Throws
     * \RuntimeException (message is a translation key) if posting isn't possible.
     */
    public function post(User $user): void
    {
        if (! $this->isDraft()) {
            throw new \RuntimeException('warehouse.voucher_not_draft');
        }

        $items = $this->items()->with('material')->get();

        if (! in_array($this->type, ['receipt', 'stocktake'], true)) {
            foreach ($items as $item) {
                $available = MaterialStock::where('material_id', $item->material_id)
                    ->where('warehouse_id', $this->warehouse_id)
                    ->value('quantity') ?? 0;

                if ($available < $item->quantity) {
                    throw new \RuntimeException('warehouse.insufficient_stock');
                }
            }
        }

        DB::transaction(function () use ($items, $user) {
            foreach ($items as $item) {
                if ($this->type === 'receipt') {
                    $this->applyMovement($item->material_id, $this->warehouse_id, 'in', $item->quantity);
                } elseif ($this->type === 'issue') {
                    $this->applyMovement($item->material_id, $this->warehouse_id, 'out', $item->quantity);
                } elseif ($this->type === 'stocktake') {
                    // $item->quantity is the physically COUNTED amount, not a delta. The movement
                    // amount is the difference against the live system balance — applying that
                    // delta as a normal increment/decrement lands the balance exactly on the
                    // counted figure, so no separate "set absolute" path is needed.
                    $current = MaterialStock::where('material_id', $item->material_id)
                        ->where('warehouse_id', $this->warehouse_id)
                        ->value('quantity') ?? 0;
                    $delta = $item->quantity - $current;

                    if ($delta > 0) {
                        $this->applyMovement($item->material_id, $this->warehouse_id, 'in', $delta);
                    } elseif ($delta < 0) {
                        $this->applyMovement($item->material_id, $this->warehouse_id, 'out', abs($delta));
                    }
                } else { // transfer
                    $this->applyMovement($item->material_id, $this->warehouse_id, 'out', $item->quantity);
                    $this->applyMovement($item->material_id, $this->destination_warehouse_id, 'in', $item->quantity);
                }
            }

            $this->update([
                'status'    => 'posted',
                'posted_by' => $user->id,
                'posted_at' => now(),
            ]);
        });
    }

    private function applyMovement(int $materialId, int $warehouseId, string $type, float $quantity): void
    {
        MaterialStockMovement::create([
            'material_id'      => $materialId,
            'warehouse_id'     => $warehouseId,
            'type'             => $type,
            'quantity'         => $quantity,
            'stock_voucher_id' => $this->id,
            'moved_at'         => now(),
        ]);

        $stock = MaterialStock::firstOrCreate(
            ['material_id' => $materialId, 'warehouse_id' => $warehouseId],
            ['quantity' => 0]
        );

        $stock->increment('quantity', $type === 'in' ? $quantity : -$quantity);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
