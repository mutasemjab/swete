<?php

namespace Tests\Feature;

use App\Imports\CustomersImport;
use App\Imports\MaterialsImport;
use App\Imports\SuppliersImport;
use App\Models\Customer;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class WorkbookImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'activitylog.enabled' => false]);
        DB::purge('sqlite');
        Schema::create('users', fn (Blueprint $table) => $table->id());
        DB::table('users')->insert(['id' => 1]);
        foreach ([
            '019001_create_countries', '019002_create_governorates',
            '020001_create_customer_groups', '020002_create_supplier_groups',
            '020003_create_customers', '020004_create_suppliers',
            '010002_create_material_categories', '010003_create_units', '010004_create_materials',
        ] as $migration) {
            (require database_path('migrations/2026_09_05_'.$migration.'_table.php'))->up();
        }
    }

    public function test_arabic_party_headers_and_numeric_identifiers_are_imported(): void
    {
        // A deleted/gapped sequence must not collide with an existing code.
        Customer::create(['code' => 'CUST-00002', 'name' => 'Existing']);
        $import = new CustomersImport(1);
        $this->importRows($import, [
            ['الاسم ', 'رقم الهاتف', 'المنطقة', 'الرقم الضريبي'],
            [' عميل تجريبي ', 5656246, ' عمان ', 93309],
            [],
            ['عميل آخر', '0790123456', null, '000123'],
        ]);
        $this->assertCount(0, $import->failures());
        $this->assertDatabaseHas('customers', ['code' => 'CUST-00003', 'name' => 'عميل تجريبي', 'phone' => '5656246', 'address' => 'عمان', 'tax_number' => '93309']);
        $this->assertDatabaseHas('customers', ['code' => 'CUST-00004', 'phone' => '0790123456', 'tax_number' => '000123']);

        $suppliers = new SuppliersImport(1);
        $this->importRows($suppliers, [['الاسم', 'رقم الهاتف'], ['مورد', '05/3491200']]);
        $this->assertCount(0, $suppliers->failures());
        $this->assertSame('05/3491200', Supplier::first()->phone);
    }

    public function test_material_report_creates_hierarchy_and_ignores_total_without_changing_stock(): void
    {
        $import = new MaterialsImport();
        $this->importRows($import, [
            ['رمزالصنف', 'اسم الصنف', 'الوحدة', 'المجموعة', 'التصنيف', 'الكمية', 'كلفةالوحدة'],
            [100001, ' ماسورة ', 'متر', 'قطع', 'حديد', 3, 66.816],
            [100002, 'قطعة أخرى', 'عدد', 'اجهزة', 'حديد', -1, 5],
            [null, 'المـجـمـوع الـنـهـائــي ', null, null, null, null, 123],
        ]);
        $this->assertCount(0, $import->failures());
        $this->assertSame(2, Material::count());
        $this->assertSame(4, MaterialCategory::count());
        $material = Material::where('code', '100001')->firstOrFail();
        $this->assertSame('ماسورة', $material->name);
        $this->assertSame('قطع', $material->category->parent->name);
        $this->assertSame('متر', $material->unit->name);
        $this->assertFalse($material->is_draft);
    }

    public function test_invalid_and_duplicate_rows_report_original_excel_row_numbers(): void
    {
        $import = new MaterialsImport();
        $this->importRows($import, [
            ['code', 'name', 'category', 'unit'],
            ['001', 'Valid', 'Parts', 'Each'],
            [],
            ['001', 'Duplicate', 'Unused category', 'Unused unit'],
            ['002', null, 'Another unused category', 'Another unused unit'],
        ]);
        $this->assertSame([4, 5], $import->failures()->map(fn ($failure) => $failure->row())->unique()->values()->all());
        $this->assertSame(1, Material::count());
        $this->assertSame(1, MaterialCategory::count());
        $this->assertSame(1, Unit::count());
    }

    public function test_english_templates_reuse_existing_translated_references(): void
    {
        $parent = MaterialCategory::create(['name' => 'Parent']);
        $category = MaterialCategory::create(['name' => 'حديد', 'name_en' => 'Iron', 'parent_id' => $parent->id]);
        $unit = Unit::create(['name' => 'متر', 'name_en' => 'Meter']);
        $import = new MaterialsImport();
        $this->importRows($import, [['code', 'name', 'name_en', 'category', 'unit'], ['00123', 'مادة', 'Material', 'Iron', 'Meter']]);
        $this->assertCount(0, $import->failures());
        $this->assertDatabaseHas('materials', ['code' => '00123', 'category_id' => $category->id, 'unit_id' => $unit->id]);
    }

    private function importRows(object $import, array $rows): void
    {
        $book = new Spreadsheet();
        $book->getActiveSheet()->fromArray($rows, null, 'A1', true);
        $path = tempnam(sys_get_temp_dir(), 'workbook-import-');
        try {
            (new Xlsx($book))->save($path);
            Excel::import($import, $path, null, \Maatwebsite\Excel\Excel::XLSX);
        } finally {
            unlink($path);
            $book->disconnectWorksheets();
        }
    }
}
