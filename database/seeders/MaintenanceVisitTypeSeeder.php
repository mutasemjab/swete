<?php

namespace Database\Seeders;

use App\Models\MaintenanceVisitType;
use Illuminate\Database\Seeder;

class MaintenanceVisitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'زيارة كفالة',                              'name_en' => 'Warranty Visit',                         'requires_note' => false],
            ['name' => 'عقد صيانة',                                'name_en' => 'Maintenance Contract',                   'requires_note' => false],
            ['name' => 'تحتاج لقطع غيار وعرض سعر وفواتير',          'name_en' => 'Needs Spare Parts, Quote & Invoices',    'requires_note' => false],
            ['name' => 'أمور أخرى',                                 'name_en' => 'Other Matters',                          'requires_note' => true],
        ];

        foreach ($types as $type) {
            MaintenanceVisitType::firstOrCreate(['name' => $type['name']], $type);
        }

        $this->command->info('✓ Maintenance visit types seeded.');
    }
}
