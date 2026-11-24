<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** One physical piece of equipment installed at a customer's site. */
class EquipmentUnit extends Model
{
    use LogsActivity;

    public const STATUSES = ['operational', 'maintenance', 'out_of_service', 'decommissioned'];

    protected $fillable = [
        'customer_id',
        'equipment_category_id',
        'equipment_subtype_id',
        'equipment_model_id',
        'name',
        'name_en',
        'serial_number',
        'location',
        'project',
        'status',
        'notes',
        'is_active',
        'created_by',
        'report_template_id',
        'reading_limits',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'reading_limits' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $unit) {
            $unit->qr_token ??= bin2hex(random_bytes(16));
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class, 'equipment_category_id');
    }

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(EquipmentSubtype::class, 'equipment_subtype_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(EquipmentModel::class, 'equipment_model_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reportTemplate(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReportTemplate::class, 'report_template_id');
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
