<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::firstOrCreate(
            ['code' => 'JOD'],
            ['name' => 'دينار أردني', 'name_en' => 'Jordanian Dinar', 'symbol' => 'د.أ', 'exchange_rate' => 1, 'is_default' => true, 'status' => true]
        );

        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'دولار أمريكي', 'name_en' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 0.71, 'is_default' => false, 'status' => true]
        );

        Currency::firstOrCreate(
            ['code' => 'EUR'],
            ['name' => 'يورو', 'name_en' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.77, 'is_default' => false, 'status' => true]
        );

        $this->command->info('✓ Currencies seeded (JOD default, USD, EUR).');
    }
}
