<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceNotification extends Model
{
    protected $fillable = ['user_id', 'kind', 'dedupe_key', 'data', 'read_at', 'emailed_at'];

    protected $casts = ['data' => 'array', 'read_at' => 'datetime', 'emailed_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMessageAttribute(): string
    {
        return __('maintenance.notification_'.$this->kind, $this->data);
    }
}
