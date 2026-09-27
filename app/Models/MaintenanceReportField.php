<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A permanent, per-report copy of one template field (question/type/options/order) plus its answer. */
class MaintenanceReportField extends Model
{
    protected $fillable = [
        'report_id',
        'question',
        'question_en',
        'type',
        'options',
        'order',
        'answer',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class, 'report_id');
    }

    public function getLocalizedQuestionAttribute(): string
    {
        return app()->isLocale('en') && $this->question_en
            ? $this->question_en
            : $this->question;
    }

    /** The answer, formatted for display per its type (boolean → yes/no, others → as typed/chosen). */
    public function getFormattedAnswerAttribute(): ?string
    {
        if ($this->answer === null || $this->answer === '') {
            return null;
        }

        return $this->type === 'boolean'
            ? ($this->answer === '1' ? __('app.yes') : __('app.no'))
            : $this->answer;
    }
}
