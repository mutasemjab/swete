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
        'is_ciat',
    ];

    protected $casts = [
        'min_stock_level' => 'decimal:3',
        'status'          => 'boolean',
        'is_draft'        => 'boolean',
        'is_ciat'         => 'boolean',
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

    /** Only materials the warehouse has flagged as CIAT products — see the CIAT Discount create/edit screens. */
    public function scopeCiat(Builder $query): Builder
    {
        return $query->where('is_ciat', true);
    }

    /** DFT-00001-style code for a quick-added draft material — never collides with a manually
     *  typed real material code since those don't use this prefix. */
    public static function nextDraftCode(): string
    {
        return static::nextCode('DFT');
    }

    /** CIAT-00001-style code for a material quick-added straight from the CIAT Discount screen —
     *  a real catalog material from the start, not a draft, so it needs its own non-colliding prefix. */
    public static function nextCiatCode(): string
    {
        return static::nextCode('CIAT');
    }

    private static function nextCode(string $prefix): string
    {
        $count = static::where('code', 'like', "{$prefix}-%")->count() + 1;

        return sprintf('%s-%05d', $prefix, $count);
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
