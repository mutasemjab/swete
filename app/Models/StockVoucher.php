<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Shared header for the 3 real stock-movement voucher types: receipt, issue, transfer.
 * `warehouse_id` is the only warehouse involved for receipt/issue; for transfer it's the
 * source and `destination_warehouse_id` is the target.
 */
class StockVoucher extends Model
{
    use LogsActivity;

    protected $fillable = [
        'type',
        'number',
        'warehouse_id',
        'destination_warehouse_id',
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
            'receipt'  => 'REC',
            'issue'    => 'ISS',
            'transfer' => 'TRF',
        };

        $year  = now()->format('Y');
        $count = static::where('type', $type)->whereYear('created_at', $year)->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }

    /**
     * Post a draft voucher: writes ledger movements and updates live balances.
     * Issue/transfer are checked for sufficient stock first. Throws \RuntimeException
     * (message is a translation key) if posting isn't possible.
     */
    public function post(User $user): void
    {
        if (! $this->isDraft()) {
            throw new \RuntimeException('warehouse.voucher_not_draft');
        }

        $items = $this->items()->with('material')->get();

        if ($this->type !== 'receipt') {
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
