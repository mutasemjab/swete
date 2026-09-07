<?php

namespace App\Models;

use App\Models\Concerns\FormatsAddressLines;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use FormatsAddressLines;

    protected $fillable = [
        'name',
        'name_en',
        'phone',
        'fax',
        'address_line1',
        'address_line1_en',
        'po_box',
        'postal_code',
        'city',
        'city_en',
        'country',
        'country_en',
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

    /** Stacked, locale-aware address lines — e.g. for a purchase request document. */
    public function getLocalizedAddressLinesAttribute(): array
    {
        return $this->addressLines(
            $this->address_line1, $this->address_line1_en,
            $this->po_box, $this->postal_code,
            $this->city, $this->city_en,
            $this->country, $this->country_en,
        );
    }
}
