<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesRepresentative extends Model
{
    protected $fillable = ['name', 'name_en', 'phone', 'commission_percent', 'status'];

    protected $casts = [
        'commission_percent' => 'decimal:2',
        'status'             => 'boolean',
    ];

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class, 'sales_rep_id');
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en ? $this->name_en : $this->name;
    }
}
