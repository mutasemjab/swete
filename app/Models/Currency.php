<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'symbol',
        'exchange_rate',
        'is_default',
        'status',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
        'is_default'    => 'boolean',
        'status'        => 'boolean',
    ];

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }
}
