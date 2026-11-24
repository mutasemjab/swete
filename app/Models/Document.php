<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** A technical document (manual, datasheet, ...) reaching one or more equipment units via its DocumentAssignment. */
class Document extends Model
{
    use LogsActivity;

    public const PREVIEWABLE_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt'];

    protected $fillable = [
        'document_type_id',
        'name',
        'name_en',
        'language',
        'revision',
        'tags',
        'path',
        'original_name',
        'content_type',
        'size',
        'uploaded_by',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Kept 1:1 for now (one target per document) — the table itself supports many,
    // so this can grow into a hasMany later with no schema change.
    public function assignment(): HasOne
    {
        return $this->hasOne(DocumentAssignment::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    public function getUrlAttribute(): ?string
    {
        return FileStorage::url($this->path);
    }

    public function getCanPreviewAttribute(): bool
    {
        $extension = strtolower(pathinfo($this->original_name ?? $this->path, PATHINFO_EXTENSION));

        return in_array($extension, self::PREVIEWABLE_EXTENSIONS, true);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
