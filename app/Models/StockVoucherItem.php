<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockVoucherItem extends Model
{
    protected $fillable = [
        'stock_voucher_id',
        'material_id',
        'quantity',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'quantity'  => 'decimal:3',
        'unit_cost' => 'decimal:4',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(StockVoucher::class, 'stock_voucher_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
