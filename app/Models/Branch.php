<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'phone',
        'address',
        'address_en',
        'is_main',
        'status',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'status'  => 'boolean',
    ];

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }
}
