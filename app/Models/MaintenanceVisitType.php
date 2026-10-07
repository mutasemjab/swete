<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** Dynamic/manageable classification list for closed maintenance visits — admin can add more any time. */
class MaintenanceVisitType extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'name_en',
        'requires_note',
        'status',
    ];

    protected $casts = [
        'requires_note' => 'boolean',
        'status'        => 'boolean',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(MaintenanceVisit::class, 'visit_type_id');
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
