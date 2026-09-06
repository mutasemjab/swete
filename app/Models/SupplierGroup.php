<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** شجرة الموردين */
class SupplierGroup extends Model
{
    use LogsActivity;

    protected $fillable = [
        'parent_id',
        'name',
        'name_en',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    public function getPathAttribute(): string
    {
        $names = [$this->localized_name];
        $node  = $this->parent;

        while ($node) {
            $names[] = $node->localized_name;
            $node    = $node->parent;
        }

        return implode(' > ', array_reverse($names));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
