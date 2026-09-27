<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReportTemplateField extends Model
{
    public const TYPES = ['number', 'text', 'boolean', 'choice'];

    protected $fillable = [
        'template_id',
        'question',
        'question_en',
        'type',
        'options',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReportTemplate::class, 'template_id');
    }

    public function getLocalizedQuestionAttribute(): string
    {
        return app()->isLocale('en') && $this->question_en
            ? $this->question_en
            : $this->question;
    }
}
