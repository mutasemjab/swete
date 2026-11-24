<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** A control system/board (e.g. "Carel Connect 2"). Alarm lists belong to the controller, not to one equipment size. */
class ControlSystem extends Model
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

    public function models(): HasMany
    {
        return $this->hasMany(EquipmentModel::class, 'control_system_id');
    }

    public function alarmCodes(): HasMany
    {
        return $this->hasMany(AlarmCode::class, 'control_system_id');
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
