<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Agreement extends Model
{
    use LogsActivity;

    protected $fillable = [
        'number',
        'party_type',
        'party_id',
        'path',
        'notes',
        'created_by',
    ];

    /** `party_type` ('customer'|'supplier') says which table `party_id` points into — same convention as Invoice::party(). */
    public function party(): BelongsTo
    {
        return $this->party_type === 'supplier'
            ? $this->belongsTo(Supplier::class, 'party_id')
            : $this->belongsTo(Customer::class, 'party_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUrlAttribute(): string
    {
        return asset($this->path);
    }

    public static function nextNumber(): string
    {
        $year  = now()->format('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;

        return sprintf('AGR-%s-%05d', $year, $count);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
