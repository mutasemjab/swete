<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** Dynamic/manageable list of tender statuses — admin can add more any time. */
class TenderStatus extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'color',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class, 'status_id');
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
