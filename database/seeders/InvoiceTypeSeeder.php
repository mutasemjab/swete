<?php

namespace Database\Seeders;

use App\Models\InvoiceType;
use Illuminate\Database\Seeder;

class InvoiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        InvoiceType::firstOrCreate(
            ['code' => 'sales_invoice'],
            [
                'name'       => 'فاتورة مبيعات',
                'name_en'    => 'Sales Invoice',
                'party_type' => 'customer',
                'is_system'  => true,
                'status'     => true,
            ]
        );

        InvoiceType::firstOrCreate(
            ['code' => 'purchase_invoice'],
            [
                'name'       => 'فاتورة مشتريات',
                'name_en'    => 'Purchase Invoice',
                'party_type' => 'supplier',
                'is_system'  => true,
                'status'     => true,
            ]
        );

        $this->command->info('✓ Default invoice types seeded.');
    }
}
