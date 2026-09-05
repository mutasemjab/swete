<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Material extends Model
{
    use LogsActivity;

    protected $fillable = [
        'category_id',
        'unit_id',
        'code',
        'name',
        'name_en',
        'description',
        'min_stock_level',
        'status',
    ];

    protected $casts = [
        'min_stock_level' => 'decimal:3',
        'status'          => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(MaterialStock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(MaterialStockMovement::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    public function stockIn(string $warehouseId): float
    {
        return (float) $this->stocks()->where('warehouse_id', $warehouseId)->value('quantity');
    }

    public function totalStock(): float
    {
        return (float) $this->stocks()->sum('quantity');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
