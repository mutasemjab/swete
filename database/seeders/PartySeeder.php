<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class PartySeeder extends Seeder
{
    public function run(): void
    {
        if (! Customer::where('name', 'معتصم')->exists()) {
            Customer::create([
                'code'   => Customer::nextCode(),
                'name'   => 'معتصم',
                'status' => true,
            ]);
        }

        if (! Supplier::where('name', 'أحمد')->exists()) {
            Supplier::create([
                'code'   => Supplier::nextCode(),
                'name'   => 'أحمد',
                'status' => true,
            ]);
        }

        $this->command->info('✓ Sample customer (معتصم) and supplier (أحمد) seeded.');
    }
}
