<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class X8 extends Model
{
    protected $table = 'x8';

    protected $fillable = [
        'price_analysis_item_id',
        'required_discount_percent',
        'notes',
        'approval_status',
    ];

    protected $casts = [
        'required_discount_percent' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PriceAnalysisItem::class, 'price_analysis_item_id');
    }
}
