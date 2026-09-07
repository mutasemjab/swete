<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            BranchSeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            InvoiceTypeSeeder::class,
            PartySeeder::class,
            TenderStatusSeeder::class,
        ]);
    }
}
