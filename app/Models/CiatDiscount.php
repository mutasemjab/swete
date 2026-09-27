<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** A warehouse material's discount % off its list price — looked up when building a Price Analysis line. */
class CiatDiscount extends Model
{
    use LogsActivity;

    protected $fillable = [
        'material_id',
        'discount_percent',
        'status',
        'created_by',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'status'           => 'boolean',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceAnalysisItem::class);
    }

    public function getLabelAttribute(): string
    {
        return $this->material?->localized_name ?? '';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
