<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** The main equipment type, e.g. "Chillers" — top of the Category > Subtype > Model > Unit hierarchy. */
class EquipmentCategory extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'name_en',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subtypes(): HasMany
    {
        return $this->hasMany(EquipmentSubtype::class);
    }

    public function models(): HasMany
    {
        return $this->hasMany(EquipmentModel::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(EquipmentUnit::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
