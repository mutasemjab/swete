<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/** A blank template with the exact columns CustomersImport expects — nothing to fill in but headers. */
class CustomersTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return ['name', 'name_en', 'phone', 'email', 'address', 'tax_number', 'opening_balance'];
    }
}
