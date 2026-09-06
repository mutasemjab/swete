<?php

namespace Database\Seeders;

use App\Models\TenderStatus;
use Illuminate\Database\Seeder;

class TenderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'design',    'name' => 'تحت التصميم',   'name_en' => 'Under Design',    'color' => 'violet'],
            ['code' => 'pricing',   'name' => 'تحت التسعير',   'name_en' => 'Under Pricing',   'color' => 'amber'],
            ['code' => 'execution', 'name' => 'تحت التنفيذ',   'name_en' => 'Under Execution', 'color' => 'blue'],
            ['code' => 'open',      'name' => 'مفتوح',         'name_en' => 'Open',            'color' => 'indigo'],
            ['code' => 'closed',    'name' => 'مغلق',          'name_en' => 'Closed',          'color' => 'slate'],
            ['code' => 'won',       'name' => 'فائز به',       'name_en' => 'Won',             'color' => 'emerald'],
            ['code' => 'lost',      'name' => 'خاسر',          'name_en' => 'Lost',            'color' => 'rose'],
        ];

        foreach ($statuses as $status) {
            TenderStatus::firstOrCreate(['code' => $status['code']], $status);
        }

        $this->command->info('✓ Tender statuses seeded.');
    }
}
