<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/** The real, current catalog — drafts (quick-added from a Price Quote/CIAT screen) excluded, same as the main index. */
class MaterialsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Material::confirmed()->with(['category', 'unit'])->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['code', 'name', 'name_en', 'category', 'unit'];
    }

    public function map($material): array
    {
        return [
            $material->code,
            $material->name,
            $material->name_en,
            $material->category?->name,
            $material->unit?->name,
        ];
    }
}
