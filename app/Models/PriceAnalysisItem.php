<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAnalysisItem extends Model
{
    protected $fillable = [
        'price_analysis_id',
        'ciat_discount_id',
        'ciat_type',
        'ciat_model',
        'quantity',
        'list_price',
        'discount_percent',
        'cost',
        'profit',
        'total_profit',
        'price',
        'to_jd',
        'shipping',
    ];

    protected $casts = [
        'quantity'         => 'decimal:3',
        'list_price'       => 'decimal:3',
        'discount_percent' => 'decimal:2',
        'cost'             => 'decimal:3',
        'profit'           => 'decimal:3',
        'total_profit'     => 'decimal:3',
        'price'            => 'decimal:3',
        'to_jd'            => 'decimal:3',
        'shipping'         => 'decimal:3',
    ];

    public function priceAnalysis(): BelongsTo
    {
        return $this->belongsTo(PriceAnalysis::class);
    }

    public function ciatDiscount(): BelongsTo
    {
        return $this->belongsTo(CiatDiscount::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->to_jd + $this->shipping);
    }

    /** cost = list price after the CIAT discount; total_profit = per-unit profit × quantity; price = cost + profit. */
    public static function calculate(float $listPrice, float $discountPercent, float $profit, float $quantity, float $jdRate): array
    {
        $cost  = round($listPrice * (1 - $discountPercent / 100), 3);
        $price = round($cost + $profit, 3);

        return [
            'cost'         => $cost,
            'total_profit' => round($profit * $quantity, 3),
            'price'        => $price,
            'to_jd'        => round($price * $jdRate, 3),
        ];
    }
}
