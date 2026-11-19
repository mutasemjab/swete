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
            WarehouseSeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            GovernorateSeeder::class,
            InvoiceTypeSeeder::class,
            TenderStatusSeeder::class,
            QuoteOptionsSeeder::class,
            AppointmentTypeSeeder::class,
            UnitSeeder::class,
            MaintenanceVisitTypeSeeder::class,
            UserSeeder::class,
        ]);
    }
}
