<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * The live, editable definition of a maintenance report's questions for one product.
 * Editing/deleting this (or its fields) never changes an already-filled MaintenanceReport —
 * see MaintenanceReportField, which permanently copies each field at fill-time.
 */
class MaintenanceReportTemplate extends Model
{
    use LogsActivity;

    protected $fillable = [
        'material_id',
        'equipment_category_id',
        'name',
        'name_en',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function isForCategory(): bool
    {
        return $this->equipment_category_id !== null;
    }

    public function getSubjectLabelAttribute(): string
    {
        return $this->isForCategory()
            ? __('maintenance.template_subject_category', ['name' => $this->category?->localized_name])
            : $this->material?->localized_name ?? '—';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(MaintenanceReportTemplateField::class, 'template_id')->orderBy('order');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class, 'template_id');
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
