<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('is_main', true)->firstOrFail();

        Warehouse::firstOrCreate(
            ['branch_id' => $branch->id, 'name' => 'مستودع البيادر'],
            ['name_en' => 'Al Bayader Warehouse', 'is_main' => true, 'status' => true]
        );
    }
}
