<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Units are seeded by UnitSeeder (runs first) — just look them up.
        $piece = Unit::where('symbol', 'قطعة')->firstOrFail();
        $lm    = Unit::where('symbol', 'م.ط')->firstOrFail();
        $sqm   = Unit::where('symbol', 'م2')->firstOrFail();
        $kg    = Unit::where('symbol', 'كغم')->firstOrFail();

        $equipment = MaterialCategory::firstOrCreate(
            ['code' => 'EQP'],
            ['name' => 'أجهزة ومعدات التكييف المركزي', 'name_en' => 'Central AC Equipment', 'status' => true]
        );

        $supplies = MaterialCategory::firstOrCreate(
            ['code' => 'SUP'],
            ['name' => 'مواد ولوازم التمديد والعزل', 'name_en' => 'Ducting & Insulation Supplies', 'status' => true]
        );

        $materials = [
            ['code' => 'MAT-001', 'name' => 'تشيلر تكييف مركزي',        'name_en' => 'Central AC Chiller',            'category_id' => $equipment->id, 'unit_id' => $piece->id],
            ['code' => 'MAT-002', 'name' => 'كمبريسور تكييف',           'name_en' => 'AC Compressor',                 'category_id' => $equipment->id, 'unit_id' => $piece->id],
            ['code' => 'MAT-003', 'name' => 'شيلات تكييف (صاج مجلفن)',  'name_en' => 'Galvanized Ducting Sheet',      'category_id' => $supplies->id,  'unit_id' => $sqm->id],
            ['code' => 'MAT-004', 'name' => 'أنابيب نحاس تكييف',        'name_en' => 'Copper Refrigeration Pipe',     'category_id' => $supplies->id,  'unit_id' => $lm->id],
            ['code' => 'MAT-005', 'name' => 'غاز فريون R410A',          'name_en' => 'Refrigerant Gas R410A',         'category_id' => $supplies->id,  'unit_id' => $kg->id],
            ['code' => 'MAT-006', 'name' => 'عازل حراري أرمافلكس',      'name_en' => 'Armaflex Thermal Insulation',   'category_id' => $supplies->id,  'unit_id' => $lm->id],
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate(
                ['code' => $material['code']],
                [...$material, 'status' => true]
            );
        }

        $this->command->info('✓ Material categories and central-AC materials seeded.');
    }
}
