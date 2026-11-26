<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MaintenanceBackupImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MaintenanceBackupImportTest extends TestCase
{
    private string $package;
    private array $source;
    private array $mapping;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null, 'filesystems.default' => 'local', 'activitylog.enabled' => false]);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
        Storage::fake('local');
        Storage::fake('maintenance_private');
        $user = User::factory()->create(['email' => 'import-test@example.test']);
        $this->package = sys_get_temp_dir().'/swete-import-unit-'.bin2hex(random_bytes(8));
        mkdir($this->package.'/files/2026', 0775, true);
        file_put_contents($this->package.'/files/2026/manual.pdf', "%PDF-1.4\nTest fixture, pre-indexed text.\n");
        $common = ['name_en' => 'Test', 'name_ar' => '', 'is_active' => 't'];
        $this->source = ['version' => 1, 'source' => 'hvac', 'files' => [
            'files/2026/manual.pdf' => ['size' => filesize($this->package.'/files/2026/manual.pdf'), 'sha256' => hash_file('sha256', $this->package.'/files/2026/manual.pdf')],
        ], 'tables' => [
            'core_user' => [['id' => '2', 'username' => 'source-technician']],
            'core_category' => [['id' => '11'] + $common],
            'core_manufacturer' => [['id' => '12', 'website' => ''] + $common],
            'core_controller' => [['id' => '13'] + $common],
            'core_documenttype' => [['id' => '14'] + $common],
            'core_client' => [['id' => '100', 'phone' => '', 'email' => '', 'address' => '', 'created_at' => '2026-11-01 12:00:00+03'] + $common],
            'core_equipmentmodel' => [['id' => '200', 'manufacturer_id' => '12', 'category_id' => '11', 'subtype_id' => null,
                'controller_id' => '13', 'model_number' => 'MODEL-TEST', 'reading_limits' => '{}', 'is_active' => 't']],
            'core_unit' => [['id' => '300', 'client_id' => '100', 'category_id' => '11', 'subtype_id' => null, 'model_id' => '200',
                'serial_number' => 'SERIAL', 'location' => 'Site', 'project' => '', 'status' => 'operational', 'notes' => '',
                'qr_token' => '0123456789abcdef0123456789abcdef', 'reading_limits' => '{}', 'created_at' => '2026-11-01 12:00:00+03'] + $common],
            'core_document' => [['id' => '400', 'doc_type_id' => '14', 'language' => 'en', 'revision' => '', 'tags' => '',
                'file' => '2026/manual.pdf', 'original_name' => 'manual.pdf', 'content_type' => 'application/pdf',
                'size' => 43, 'uploaded_by_id' => '2', 'is_archived' => 'f', 'uploaded_at' => '2026-11-01 12:00:00+03',
                'updated_at' => '2026-11-01 12:00:00+03'] + $common],
            'core_documentassignment' => [
                ['id' => '500', 'level' => 'unit', 'unit_id' => '300', 'model_id' => null],
                ['id' => '501', 'level' => 'model', 'unit_id' => null, 'model_id' => '200'],
            ],
            'core_documentpage' => [['id' => '600', 'document_id' => '400', 'number' => '4', 'text' => 'P123 source setting']],
        ]];
        foreach ($this->source['tables']['core_documentassignment'] as &$row) {
            $row += ['document_id' => '400', 'created_by_id' => '2', 'created_at' => '2026-11-01 12:00:00+03',
                'category_id' => null, 'subtype_id' => null, 'manufacturer_id' => null];
        }
        unset($row);
        $this->mapping = ['actor_user_id' => $user->id, 'users' => ['2' => $user->id], 'user_emails' => ['2' => $user->email], 'map' => []];
    }

    protected function tearDown(): void
    {
        // Only exact fixture files created by this test; never recursive deletion.
        foreach (['files/2026/manual.pdf', 'data.json.gz', 'manifest.json'] as $file) {
            if (is_file($this->package.'/'.$file)) unlink($this->package.'/'.$file);
        }
        rmdir($this->package.'/files/2026'); rmdir($this->package.'/files'); rmdir($this->package);
        parent::tearDown();
    }

    private function importer(): MaintenanceBackupImport
    {
        $raw = json_encode($this->source, JSON_THROW_ON_ERROR);
        $this->mapping['source_sha256'] = hash('sha256', $raw);
        file_put_contents($this->package.'/data.json.gz', gzencode($raw));
        file_put_contents($this->package.'/manifest.json', json_encode(['sha256' => $this->mapping['source_sha256']]));
        $import = new MaintenanceBackupImport();
        $import->load($this->package, $this->mapping);
        return $import;
    }

    public function test_import_preserves_customer_identity_and_multiple_manual_links_and_retries_without_duplicates(): void
    {
        $existing = DB::table('customers')->insertGetId(['code' => 'EXISTING', 'name' => 'Test', 'opening_balance' => 123.456, 'address' => 'Preserve me']);
        $this->mapping['map']['customers']['100'] = $existing;
        $before = (array) DB::table('customers')->where('id', $existing)->first();
        $import = $this->importer();
        $this->assertSame([], $import->preview()['blockers']);
        $this->assertFalse($import->apply()['already_imported']);
        $this->assertTrue($import->apply()['already_imported']);
        $this->assertSame($before, (array) DB::table('customers')->where('id', $existing)->first());
        $this->assertSame(1, DB::table('customers')->count());
        $this->assertSame($existing, DB::table('equipment_units')->value('customer_id'));
        $this->assertSame(2, DB::table('document_assignments')->count());
        $document = \App\Models\Document::first();
        $this->assertSame([['number' => 4, 'text' => 'P123 source setting']], app(\App\Support\MaintenanceDocumentText::class)->pages($document));
        $this->assertCount(1, \App\Support\DocumentInheritance::forUnit(\App\Models\EquipmentUnit::first()));
        $this->assertSame(0, DB::table('maintenance_notifications')->count());
    }

    public function test_read_only_preview_and_explicit_create_preserve_same_named_existing_customer(): void
    {
        DB::table('customers')->insert(['code' => 'EXISTING', 'name' => 'Test']);
        $this->assertNotEmpty($this->importer()->preview()['blockers']);
        $this->assertSame(0, DB::table('maintenance_import_runs')->count());
        $this->assertSame(0, DB::table('equipment_units')->count());
        $this->mapping['map']['customers']['100'] = 'create';
        $this->importer()->apply();
        $this->assertSame(2, DB::table('customers')->count());
        $this->assertDatabaseHas('customers', ['code' => 'EXISTING', 'name' => 'Test']);
        $this->assertDatabaseHas('customers', ['code' => 'HVAC-100', 'name' => 'Test']);
    }

    public function test_late_failure_rolls_back_database_and_cleans_only_files_created_by_this_import(): void
    {
        Storage::put('existing-file.txt', 'Keep');
        $this->source['tables']['core_contract'] = [['id' => '700', 'number' => 'C1', 'client_id' => 'missing']];
        try {
            $this->importer()->apply();
            $this->fail('Expected missing reference to abort import.');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('Unmapped reference', $error->getMessage());
        }
        $this->assertSame(0, DB::table('customers')->count());
        $this->assertSame(0, DB::table('documents')->count());
        $this->assertSame(0, DB::table('maintenance_import_runs')->count());
        $this->assertSame(['existing-file.txt'], Storage::allFiles());
        $this->assertSame([], Storage::disk('maintenance_private')->allFiles());
    }

    public function test_user_email_mismatch_blocks_import_even_if_id_exists(): void
    {
        $this->mapping['user_emails']['2'] = 'wrong-person@example.test';
        $import = $this->importer();
        $this->assertStringContainsString('User ID/email mismatch', implode(' ', $import->preview()['blockers']));
        $this->expectException(\RuntimeException::class);
        $import->apply();
    }

    public function test_corrupt_attachment_is_rejected_before_database_changes(): void
    {
        file_put_contents($this->package.'/files/2026/manual.pdf', 'Changed');
        $this->expectExceptionMessage('Missing or changed package file');
        $this->importer();
    }

    public function test_mapping_existing_reports_cannot_overwrite_or_append_fields_to_them(): void
    {
        $this->mapping['map']['maintenance_reports']['1'] = 1;
        $import = $this->importer();
        $this->assertStringContainsString('cannot be rewritten', implode(' ', $import->preview()['blockers']));
        $this->expectException(\RuntimeException::class);
        $import->apply();
    }

    public function test_editing_imported_document_metadata_keeps_all_links_and_rejects_scope_loss(): void
    {
        $this->importer()->apply();
        $user = User::first();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'equipment.documents.edit', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $this->actingAs($user);
        app('url')->forceRootUrl('http://localhost');
        $document = \App\Models\Document::first();
        $payload = ['document_type_id' => $document->document_type_id, 'name' => 'Revised title', 'language' => 'en',
            'level' => 'unit', 'equipment_unit_id' => \App\Models\EquipmentUnit::first()->id];
        $this->put(route('documents.update', $document), $payload)->assertRedirect();
        $this->assertSame('Revised title', $document->fresh()->name);
        $this->assertSame(2, $document->assignments()->count());
        $this->putJson(route('documents.update', $document), [...$payload, 'name' => 'Should not change', 'level' => 'all'])->assertUnprocessable();
        $this->assertSame('Revised title', $document->fresh()->name);
        $this->assertSame(2, $document->assignments()->count());
    }

    public function test_imported_alarm_catalogue_does_not_duplicate_the_bundled_mobile_catalogue(): void
    {
        $this->importer()->apply();
        $catalogue = json_decode(file_get_contents(resource_path('maintenance/reference/alarms.json')), true);
        $source = array_combine($catalogue['fields'], $catalogue['alarms'][0]);
        $controller = \App\Models\ControlSystem::first();
        $controller->update(['name_en' => $source['controller']]);
        $alarm = array_diff_key($source, ['controller' => true]);
        $alarm += ['control_system_id' => $controller->id, 'source_document_id' => \App\Models\Document::first()->id, 'source_page' => 1];
        DB::table('alarm_codes')->insert($alarm);
        $user = User::first();
        $user->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'maintenance.reports.create', 'guard_name' => 'web']));
        $this->actingAs($user);
        app('url')->forceRootUrl('http://localhost');
        $copies = count(array_filter($catalogue['alarms'], fn ($row) => $row === $catalogue['alarms'][0]));
        $this->getJson(route('maintenance-visits.mobile.alarms'))->assertOk()->assertJsonCount(1, 'database')->assertJsonCount(count($catalogue['alarms']) - $copies, 'alarms');
    }
}
