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
                'name_en'          => 'Main Branch',
                'phone'            => '+962-6-5855959',
                'fax'              => '+962-6-5814455',
                'address_line1'    => 'ضاحية الأمير راشد، شارع الأمير ثروت، مركز جوانا 75 - مكتب 102',
                'address_line1_en' => 'Princes Rashed Suburb, Prince Tharwat St., 75 Joanna Center - Office 102',
                'po_box'           => '108',
                'postal_code'      => '11831',
                'city'             => 'عمّان',
                'city_en'          => 'Amman',
                'country'          => 'الأردن',
                'country_en'       => 'Jordan',
                'is_main'          => true,
                'status'           => true,
            ]
        );

        $this->command->info('✓ Main branch seeded.');
    }
}
