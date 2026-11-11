<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Supplier::orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['code', 'name', 'name_en', 'phone', 'email', 'address', 'tax_number', 'opening_balance', 'status'];
    }

    public function map($supplier): array
    {
        return [
            $supplier->code,
            $supplier->name,
            $supplier->name_en,
            $supplier->phone,
            $supplier->email,
            $supplier->address,
            $supplier->tax_number,
            $supplier->opening_balance,
            $supplier->status ? 'active' : 'inactive',
        ];
    }
}
