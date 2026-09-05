<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MaterialCategory extends Model
{
    use LogsActivity;

    protected $fillable = [
        'parent_id',
        'name',
        'name_en',
        'code',
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

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'category_id');
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    /** Full "Parent > Child" breadcrumb path, root first. */
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
