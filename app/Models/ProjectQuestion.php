<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectQuestion extends Model
{
    protected $fillable = [
        'project_id',
        'question',
        'asked_to',
        'asked_by',
        'answer',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function askedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asked_to');
    }

    public function askedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asked_by');
    }
}
