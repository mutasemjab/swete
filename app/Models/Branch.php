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
        'logo_path',
        'logo_secondary_path',
        'quote_header_image1_path',
        'quote_header_image2_path',
        'quote_header_image3_path',
        'quote_body_image1_path',
        'quote_body_image2_path',
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

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset($this->logo_path) : null;
    }

    public function getLogoSecondaryUrlAttribute(): ?string
    {
        return $this->logo_secondary_path ? asset($this->logo_secondary_path) : null;
    }

    /** Up to 3 header images for the price-quote print document — whichever are uploaded, in order, no gaps for missing ones. */
    public function getQuoteHeaderImagesAttribute(): array
    {
        return array_values(array_filter([
            $this->quote_header_image1_path ? asset($this->quote_header_image1_path) : null,
            $this->quote_header_image2_path ? asset($this->quote_header_image2_path) : null,
            $this->quote_header_image3_path ? asset($this->quote_header_image3_path) : null,
        ]));
    }

    /** The 2 additional body images shown only on the price-quote print's first page. */
    public function getQuoteBodyImagesAttribute(): array
    {
        return array_values(array_filter([
            $this->quote_body_image1_path ? asset($this->quote_body_image1_path) : null,
            $this->quote_body_image2_path ? asset($this->quote_body_image2_path) : null,
        ]));
    }
}
