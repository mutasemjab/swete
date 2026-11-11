<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAttachment extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'path',
        'created_by',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUrlAttribute(): ?string
    {
        return FileStorage::url($this->path);
    }
}
