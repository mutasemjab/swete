<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tender extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'title',
        'title_en',
        'entity_name',
        'entity_name_en',
        'submission_deadline',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'submission_deadline' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getLocalizedTitleAttribute(): string
    {
        return app()->isLocale('en') && $this->title_en
            ? $this->title_en
            : $this->title;
    }

    public function getLocalizedEntityNameAttribute(): string
    {
        return app()->isLocale('en') && $this->entity_name_en
            ? $this->entity_name_en
            : $this->entity_name;
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('TND-%s-%05d', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
