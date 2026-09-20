<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** Dynamic/manageable list of appointment types — admin can add more any time. */
class AppointmentType extends Model
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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    /** Active types for a dropdown, plus the record's current one so an inactive value isn't silently dropped on edit. */
    public static function selectable(?int $currentId = null)
    {
        return static::where('status', true)
            ->when($currentId, fn ($query) => $query->orWhere('id', $currentId))
            ->orderBy('name')
            ->get();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
