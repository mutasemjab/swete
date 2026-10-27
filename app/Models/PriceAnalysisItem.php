<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PriceAnalysisItem extends Model
{
    protected $fillable = [
        'price_analysis_id',
        'source_type',
        'ciat_discount_id',
        'material_id',
        'ciat_model',
        'quantity',
        'list_price',
        'discount_percent',
        'min_profit_percent',
        'cost',
        'profit_percent',
        'profit',
        'total_profit',
        'price',
        'to_jd',
        'shipping',
        'required_discount_percent',
        'notes',
    ];

    protected $casts = [
        'quantity'           => 'decimal:3',
        'list_price'         => 'decimal:3',
        'discount_percent'   => 'decimal:2',
        'min_profit_percent' => 'decimal:2',
        'cost'               => 'decimal:3',
        'profit_percent'     => 'decimal:2',
        'profit'             => 'decimal:3',
        'total_profit'       => 'decimal:3',
        'price'              => 'decimal:3',
        'to_jd'              => 'decimal:3',
        'shipping'           => 'decimal:3',
        'required_discount_percent' => 'decimal:2',
    ];

    public function priceAnalysis(): BelongsTo
    {
        return $this->belongsTo(PriceAnalysis::class);
    }

    /** The X8-module decision (required discount %, notes, approval status) for THIS specific item. */
    public function x8(): HasOne
    {
        return $this->hasOne(X8Item::class, 'price_analysis_item_id');
    }

    public function ciatDiscount(): BelongsTo
    {
        return $this->belongsTo(CiatDiscount::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) ($this->to_jd + $this->shipping);
    }

    /** The % of list price actually charged, i.e. 100 − the discount % — what the analysis displays (10% discount shows as 90%). */
    public function getPricePercentAttribute(): float
    {
        return round(100 - (float) $this->discount_percent, 2);
    }

    /**
     * "X8" module's Cost Price column — L.P × the discount % this line's CIAT discount actually
     * carried when it was added (0 for a warehouse-sourced line, which has no discount at all).
     * Deliberately NOT the same as `cost` above (which also folds in min_profit_percent) — the
     * user asked for this specific, simpler figure under its own name.
     */
    public function getCostPriceAttribute(): float
    {
        return round((float) $this->list_price * (float) $this->discount_percent / 100, 3);
    }

    /**
     * cost = list price after the CIAT discount, inflated by the discount's own enforced minimum
     * profit % (0 for a warehouse-sourced line, which has neither); profit = cost × profit % (a
     * markup, not a flat amount typed directly — see the 2026-10-04 change); total_profit =
     * per-unit profit × quantity; price = cost + profit.
     */
    public static function calculate(float $listPrice, float $discountPercent, float $minProfitPercent, float $profitPercent, float $quantity, float $jdRate): array
    {
        $cost   = round($listPrice * (1 - $discountPercent / 100) * (1 + $minProfitPercent / 100), 3);
        $profit = round($cost * $profitPercent / 100, 3);
        $price  = round($cost + $profit, 3);

        return [
            'cost'         => $cost,
            'profit'       => $profit,
            'total_profit' => round($profit * $quantity, 3),
            'price'        => $price,
            'to_jd'        => round($price * $jdRate, 3),
        ];
    }
}
