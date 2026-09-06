<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate(
            ['name' => 'الفرع الرئيسي'],
            [
                'name_en' => 'Main Branch',
                'is_main' => true,
                'status'  => true,
            ]
        );

        $this->command->info('✓ Main branch seeded.');
    }
}
