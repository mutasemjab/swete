<?php

namespace Database\Seeders;

use App\Models\AppointmentType;
use Illuminate\Database\Seeder;

class AppointmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        AppointmentType::firstOrCreate(['name' => 'لوجستيك هندسي'], ['name_en' => 'Engineering Logistics']);

        $this->command->info('✓ Appointment types seeded (Engineering Logistics).');
    }
}
