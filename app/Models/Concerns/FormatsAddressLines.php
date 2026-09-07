<?php

namespace App\Models\Concerns;

/**
 * Turns a set of structured address columns (street/building line, P.O. box,
 * postal code, city, country — each with an optional _en counterpart) into
 * an ordered list of printable, locale-aware lines, e.g. for a purchase
 * request document where the address needs to be stacked line by line.
 */
trait FormatsAddressLines
{
    protected function addressLines(
        ?string $line1,
        ?string $line1En,
        ?string $poBox,
        ?string $postalCode,
        ?string $city,
        ?string $cityEn,
        ?string $country,
        ?string $countryEn,
    ): array {
        $en = app()->isLocale('en');

        $resolvedLine1   = ($en && $line1En) ? $line1En : $line1;
        $resolvedCity    = ($en && $cityEn) ? $cityEn : $city;
        $resolvedCountry = ($en && $countryEn) ? $countryEn : $country;

        $poBoxLine = collect([
            $poBox ? ($en ? "P.O. Box {$poBox}" : "ص.ب {$poBox}") : null,
            $postalCode,
        ])->filter()->implode(' - ');

        $cityCountryLine = collect([$resolvedCity, $resolvedCountry])->filter()->implode(' - ');

        return collect([$resolvedLine1, $poBoxLine, $cityCountryLine])->filter()->values()->all();
    }
}
