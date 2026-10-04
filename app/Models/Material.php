<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'photo_path',
        'min_stock_level',
        'status',
        'is_draft',
    ];

    protected $casts = [
        'min_stock_level' => 'decimal:3',
        'status'          => 'boolean',
        'is_draft'        => 'boolean',
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

    public function reportTemplates(): HasMany
    {
        return $this->hasMany(MaintenanceReportTemplate::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset($this->photo_path) : null;
    }

    /** Real, catalog-ready materials only — excludes drafts quick-added from a Price Quote/CIAT
     *  Discount screen that haven't been promoted yet (see Tender::convertToProject()). Use this
     *  on every screen that lists materials for an actual stock operation (vouchers, material
     *  requests, report templates, reports' materials-used picker, purchasing) — NOT on the
     *  Price Quote/CIAT Discount pickers themselves, which must keep showing drafts too. */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('is_draft', false);
    }

    /** DFT-00001-style code for a quick-added draft material — never collides with a manually
     *  typed real material code since those don't use this prefix. */
    public static function nextDraftCode(): string
    {
        $count = static::where('code', 'like', 'DFT-%')->count() + 1;

        return sprintf('DFT-%05d', $count);
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
