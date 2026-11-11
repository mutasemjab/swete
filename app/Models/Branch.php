<?php

namespace App\Models;

use App\Models\Concerns\FormatsAddressLines;
use App\Support\FileStorage;
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
        'ciat_tax_rate',
        'ciat_jd_rate',
        'status',
    ];

    protected $casts = [
        'is_main'       => 'boolean',
        'ciat_tax_rate' => 'decimal:2',
        'ciat_jd_rate'  => 'decimal:4',
        'status'        => 'boolean',
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
        return FileStorage::url($this->logo_path);
    }

    public function getLogoSecondaryUrlAttribute(): ?string
    {
        return FileStorage::url($this->logo_secondary_path);
    }

    /** Up to 3 header images for the price-quote print document — whichever are uploaded, in order, no gaps for missing ones. */
    public function getQuoteHeaderImagesAttribute(): array
    {
        return array_values(array_filter([
            FileStorage::url($this->quote_header_image1_path),
            FileStorage::url($this->quote_header_image2_path),
            FileStorage::url($this->quote_header_image3_path),
        ]));
    }

    /** The 2 additional body images shown only on the price-quote print's first page. */
    public function getQuoteBodyImagesAttribute(): array
    {
        return array_values(array_filter([
            FileStorage::url($this->quote_body_image1_path),
            FileStorage::url($this->quote_body_image2_path),
        ]));
    }
}
