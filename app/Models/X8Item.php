<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class X8Item extends Model
{
    protected $table = 'x8_items';

    protected $fillable = [
        'x8_id',
        'price_analysis_item_id',
        'required_discount_percent',
        'notes',
        'approval_status',
    ];

    protected $casts = [
        'required_discount_percent' => 'decimal:2',
        'notes'                     => 'array',
    ];

    public function x8(): BelongsTo
    {
        return $this->belongsTo(X8::class, 'x8_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PriceAnalysisItem::class, 'price_analysis_item_id');
    }
}
