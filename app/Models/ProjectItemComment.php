<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItemComment extends Model
{
    protected $fillable = [
        'project_item_assignment_id',
        'user_id',
        'body',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ProjectItemAssignment::class, 'project_item_assignment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
