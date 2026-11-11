<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Customer::orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['code', 'name', 'name_en', 'phone', 'email', 'address', 'tax_number', 'opening_balance', 'status'];
    }

    public function map($customer): array
    {
        return [
            $customer->code,
            $customer->name,
            $customer->name_en,
            $customer->phone,
            $customer->email,
            $customer->address,
            $customer->tax_number,
            $customer->opening_balance,
            $customer->status ? 'active' : 'inactive',
        ];
    }
}
