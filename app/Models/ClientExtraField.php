<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientExtraField extends Model
{
    protected $fillable = ['entity_type', 'label_ar', 'label_en', 'field_type', 'sort_order', 'status'];

    protected $casts = ['status' => 'boolean'];

    public const FIELD_TYPES = ['text', 'number', 'date', 'textarea', 'image'];

    public function getLocalizedLabelAttribute(): string
    {
        return app()->isLocale('en') && $this->label_en ? $this->label_en : $this->label_ar;
    }

    public function values(): HasMany
    {
        return $this->hasMany(ClientExtraFieldValue::class, 'field_id');
    }

    public function scopeForCustomers(Builder $query): Builder
    {
        return $query->where('entity_type', 'customer');
    }

    public function scopeForSuppliers(Builder $query): Builder
    {
        return $query->where('entity_type', 'supplier');
    }

    public function scopeForTenders(Builder $query): Builder
    {
        return $query->where('entity_type', 'tender');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
