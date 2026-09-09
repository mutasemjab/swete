<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A singleton settings record — see VendorEmailTemplateController, always exactly one row. */
class VendorEmailTemplate extends Model
{
    protected $fillable = [
        'subject',
        'body',
        'updated_by',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** The current template, or a sensible unsaved default if nothing has been configured yet. */
    public static function current(): self
    {
        return static::first() ?? new static([
            'subject' => 'Purchase Order :number',
            'body'    => "Dear Sir/Madam,\n\nPlease find attached our purchase order :number.\n\nKind regards.",
        ]);
    }

    /** Substitute the :number placeholder with a real purchase request number. */
    public function forNumber(string $number): array
    {
        return [
            'subject' => str_replace(':number', $number, $this->subject),
            'body'    => str_replace(':number', $number, $this->body),
        ];
    }
}
