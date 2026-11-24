<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * One alarm exactly as a manual gives it. A blank field means the manual does not say — never
 * invent a value here. `source_document`/`source_page` are required so every entry is traceable.
 */
class AlarmCode extends Model
{
    use LogsActivity;

    protected $fillable = [
        'control_system_id',
        'code',
        'key',
        'section',
        'name_en',
        'name_ar',
        'causes_en',
        'causes_ar',
        'checks_en',
        'checks_ar',
        'effect_en',
        'effect_ar',
        'action_en',
        'action_ar',
        'reset_en',
        'reset_ar',
        'source_document_id',
        'source_page',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $alarm) {
            $alarm->key = self::normalizeKey($alarm->code);
        });
    }

    /** Strips all punctuation/spaces and uppercases, so "D4.1", "d4 1", "d41" all normalize the same. */
    public static function normalizeKey(string $code): string
    {
        return strtoupper(preg_replace('/[^0-9A-Za-z]/', '', $code));
    }

    public function controlSystem(): BelongsTo
    {
        return $this->belongsTo(ControlSystem::class, 'control_system_id');
    }

    public function sourceDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'source_document_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en ? $this->name_en : ($this->name_ar ?: $this->name_en);
    }

    public function getCausesAttribute(): ?string
    {
        return app()->isLocale('en') ? ($this->causes_en ?: $this->causes_ar) : ($this->causes_ar ?: $this->causes_en);
    }

    public function getChecksAttribute(): ?string
    {
        return app()->isLocale('en') ? ($this->checks_en ?: $this->checks_ar) : ($this->checks_ar ?: $this->checks_en);
    }

    public function getEffectAttribute(): ?string
    {
        return app()->isLocale('en') ? ($this->effect_en ?: $this->effect_ar) : ($this->effect_ar ?: $this->effect_en);
    }

    public function getActionAttribute(): ?string
    {
        return app()->isLocale('en') ? ($this->action_en ?: $this->action_ar) : ($this->action_ar ?: $this->action_en);
    }

    public function getResetAttribute(): ?string
    {
        return app()->isLocale('en') ? ($this->reset_en ?: $this->reset_ar) : ($this->reset_ar ?: $this->reset_en);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
