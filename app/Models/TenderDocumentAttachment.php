<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** `kind` distinguishes the tender's two document lists ('documents' vs 'design') — a plain enum
 *  column on a real FK relation, not a polymorphic/convention-based relation. */
class TenderDocumentAttachment extends Model
{
    protected $fillable = [
        'tender_id',
        'kind',
        'path',
        'original_name',
        'created_by',
    ];

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
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
