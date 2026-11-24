<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** One manufacturer's model/part number, e.g. "Carrier 30XA-502" — shared by every installed EquipmentUnit of that model. */
class EquipmentModel extends Model
{
    use LogsActivity;

    protected $fillable = [
        'equipment_manufacturer_id',
        'equipment_category_id',
        'equipment_subtype_id',
        'control_system_id',
        'model_number',
        'status',
        'reading_limits',
    ];

    protected $casts = [
        'status'         => 'boolean',
        'reading_limits' => 'array',
    ];

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(EquipmentManufacturer::class, 'equipment_manufacturer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(EquipmentSubtype::class, 'equipment_subtype_id');
    }

    public function controlSystem(): BelongsTo
    {
        return $this->belongsTo(ControlSystem::class, 'control_system_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(EquipmentUnit::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->manufacturer?->localized_name ?? '').' '.$this->model_number);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
