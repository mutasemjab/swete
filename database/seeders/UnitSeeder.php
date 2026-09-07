<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['symbol' => 'قطعة',   'name' => 'قطعة',    'name_en' => 'Piece'],
            ['symbol' => 'حبة',    'name' => 'حبة',     'name_en' => 'Item'],
            ['symbol' => 'كرتونة', 'name' => 'كرتونة',  'name_en' => 'Carton'],
            ['symbol' => 'م.ط',    'name' => 'متر طولي', 'name_en' => 'Linear Meter'],
            ['symbol' => 'م2',     'name' => 'متر مربع', 'name_en' => 'Square Meter'],
            ['symbol' => 'كغم',    'name' => 'كيلو',    'name_en' => 'Kilogram'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol']], [...$unit, 'status' => true]);
        }

        $this->command->info('✓ Units of measure seeded.');
    }
}
