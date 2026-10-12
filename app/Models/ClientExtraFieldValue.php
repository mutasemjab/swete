<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientExtraFieldValue extends Model
{
    protected $fillable = ['field_id', 'entity_type', 'entity_id', 'value', 'file_path'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(ClientExtraField::class, 'field_id');
    }
}
