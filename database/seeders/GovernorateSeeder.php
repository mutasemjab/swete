<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = [
            ['name' => 'عمّان',    'name_en' => 'Amman'],
            ['name' => 'إربد',     'name_en' => 'Irbid'],
            ['name' => 'الزرقاء',  'name_en' => 'Zarqa'],
            ['name' => 'البلقاء',  'name_en' => 'Balqa'],
            ['name' => 'المفرق',   'name_en' => 'Mafraq'],
            ['name' => 'الكرك',    'name_en' => 'Karak'],
            ['name' => 'جرش',     'name_en' => 'Jerash'],
            ['name' => 'عجلون',   'name_en' => 'Ajloun'],
            ['name' => 'مادبا',    'name_en' => 'Madaba'],
            ['name' => 'الطفيلة',  'name_en' => 'Tafilah'],
            ['name' => 'معان',    'name_en' => "Ma'an"],
            ['name' => 'العقبة',   'name_en' => 'Aqaba'],
        ];

        foreach ($governorates as $governorate) {
            Governorate::firstOrCreate(
                ['name_en' => $governorate['name_en']],
                ['name' => $governorate['name'], 'status' => true]
            );
        }

        $this->command->info('✓ Jordan governorates seeded (12).');
    }
}
