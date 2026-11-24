<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EquipmentManufacturer extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'name_en',
        'website',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(EquipmentModel::class);
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
