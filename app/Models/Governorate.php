<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Governorate extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'name_en',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getLocalizedNameAttribute(): string
    {
        return app()->isLocale('en') && $this->name_en
            ? $this->name_en
            : $this->name;
    }

    /** Active governorates for a dropdown, plus the record's current one so an inactive value isn't silently dropped on edit. */
    public static function selectable(?int $currentId = null)
    {
        return static::where('status', true)
            ->when($currentId, fn ($query) => $query->orWhere('id', $currentId))
            ->orderBy('name')
            ->get();
    }

    /** True if any tender, supplier or purchase request still points at this governorate. */
    public function isInUse(): bool
    {
        return Tender::where('governorate_id', $this->id)->exists()
            || Supplier::where('governorate_id', $this->id)->exists()
            || PurchaseRequest::where('governorate_id', $this->id)->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logAll();
    }
}
