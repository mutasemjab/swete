<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'فلسطين', 'name_en' => 'Palestine'],
            ['name' => 'قطر',    'name_en' => 'Qatar'],
            ['name' => 'العراق', 'name_en' => 'Iraq'],
            ['name' => 'الجزائر', 'name_en' => 'Algeria'],
            ['name' => 'اليمن',  'name_en' => 'Yemen'],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(
                ['name_en' => $country['name_en']],
                ['name' => $country['name'], 'status' => true]
            );
        }

        $this->command->info('✓ Countries seeded (Palestine, Qatar, Iraq, Algeria, Yemen).');
    }
}
