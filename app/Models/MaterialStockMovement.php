<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialStockMovement extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'material_id',
        'warehouse_id',
        'type',
        'quantity',
        'stock_voucher_id',
        'moved_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'moved_at' => 'datetime',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockVoucher(): BelongsTo
    {
        return $this->belongsTo(StockVoucher::class);
    }
}
