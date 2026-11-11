<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/** A blank template with the exact columns MaterialsImport expects — nothing to fill in but headers. */
class MaterialsTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return ['code', 'name', 'name_en', 'category', 'unit'];
    }
}
