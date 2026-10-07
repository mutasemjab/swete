<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/** Simple reference storage for maintenance-related device credentials — not a security vault. */
class MaintenanceDevicePassword extends Model
{
    use LogsActivity;

    protected $fillable = [
        'device_name',
        'password',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'password' => 'encrypted',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        // The 'encrypted' cast re-encrypts with a fresh IV on every save, so comparing raw stored
        // values would always look "changed" even when the decrypted plaintext didn't — exclude it.
        return LogOptions::defaults()->logOnlyDirty()->logExcept(['password'])->logAll();
    }
}
