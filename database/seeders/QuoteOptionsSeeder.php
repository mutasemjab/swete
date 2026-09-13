<?php

namespace Database\Seeders;

use App\Models\QuoteDeliveryTerm;
use App\Models\QuoteSupplyScope;
use Illuminate\Database\Seeder;

class QuoteOptionsSeeder extends Seeder
{
    public function run(): void
    {
        QuoteSupplyScope::firstOrCreate(['name' => 'توريد فقط'], ['name_en' => 'Supply Only']);
        QuoteDeliveryTerm::firstOrCreate(['name' => 'CFR ميناء العقبة'], ['name_en' => 'CFR Aqaba Port']);

        $this->command->info('✓ Price quote supply-scope/delivery-term options seeded.');
    }
}
