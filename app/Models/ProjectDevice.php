<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDevice extends Model
{
    protected $fillable = [
        'project_id',
        'price_quote_item_id',
        'supply_date',
        'installation_date',
        'warranty_start_date',
        'created_by',
    ];

    protected $casts = [
        'supply_date'          => 'date',
        'installation_date'    => 'date',
        'warranty_start_date'  => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PriceQuoteItem::class, 'price_quote_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
