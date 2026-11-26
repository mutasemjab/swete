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
        foreach (['files/2026/manual.pdf', 'data.json.gz', 'manifest.json', 'bundle.php'] as $file) {
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

    private function embeddedImporter(): MaintenanceBackupImport
    {
        $raw = json_encode($this->source, JSON_THROW_ON_ERROR);
        $this->mapping['source_sha256'] = hash('sha256', $raw);
        $envelope = ['sha256' => $this->mapping['source_sha256'], 'mapping' => $this->mapping, 'data' => base64_encode(gzencode($raw))];
        file_put_contents($this->package.'/bundle.php', '<?php return '.var_export($envelope, true).';');
        $import = new MaintenanceBackupImport();
        $import->loadEmbedded($this->package.'/bundle.php');
        return $import;
    }

    private function sourceAlarm(array $overrides = []): array
    {
        return $overrides + ['id' => '700', 'controller_id' => '13', 'source_document_id' => '400',
            'code' => 'P1', 'key' => 'P1', 'section' => '25/30 kW module', 'source_page' => '97',
            'name_en' => 'Low pressure protection system A', 'name_ar' => '',
            'causes_en' => '', 'causes_ar' => '', 'checks_en' => '', 'checks_ar' => '',
            'effect_en' => '', 'effect_ar' => '', 'action_en' => '', 'action_ar' => '', 'reset_en' => '', 'reset_ar' => ''];
    }

    public function test_same_alarm_code_and_name_are_kept_separate_for_different_manual_sections_or_pages(): void
    {
        $this->source['tables']['core_alarmcode'] = [
            $this->sourceAlarm(),
            $this->sourceAlarm(['id' => '701', 'section' => '55/60/65 kW module']),
            $this->sourceAlarm(['id' => '702', 'source_page' => '100']),
        ];
        $import = $this->embeddedImporter();
        $import->apply();
        $before = DB::table('alarm_codes')->orderBy('id')->get()->toJson();
        $this->assertTrue($import->apply()['already_imported']);
        $this->assertSame($before, DB::table('alarm_codes')->orderBy('id')->get()->toJson());
        $this->assertSame(3, DB::table('alarm_codes')->count());
        $this->assertSame(3, DB::table('maintenance_import_records')->where('source_table', 'core_alarmcode')->distinct()->count('target_id'));
        foreach ($this->source['tables']['core_alarmcode'] as $row) {
            $this->assertDatabaseHas('alarm_codes', ['code' => $row['code'], 'name_en' => $row['name_en'],
                'section' => $row['section'], 'source_page' => $row['source_page']]);
        }
        $duplicate = (array) DB::table('alarm_codes')->first();
        unset($duplicate['id']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::table('alarm_codes')->insert($duplicate);
    }

    public function test_conflicting_alarm_content_at_the_same_manual_location_still_aborts_the_import(): void
    {
        $this->source['tables']['core_alarmcode'] = [
            $this->sourceAlarm(),
            $this->sourceAlarm(['id' => '701', 'action_en' => 'Different source instruction']),
        ];
        try {
            $this->embeddedImporter()->apply();
            $this->fail('Conflicting alarm instructions must not be silently merged or replaced.');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('existing action_en differs', $error->getMessage());
        }
        $this->assertSame(0, DB::table('alarm_codes')->count());
        $this->assertSame(0, DB::table('maintenance_import_runs')->count());
        $this->assertSame(0, DB::table('customers')->count());
    }

    public function test_alarm_location_migration_preserves_existing_entries_and_refuses_lossy_rollback(): void
    {
        $migration = require database_path('migrations/2026_11_26_120001_include_manual_location_in_alarm_identity.php');
        $migration->down(); // Exercise the upgrade of an already populated old schema.
        $this->source['tables']['core_alarmcode'] = [$this->sourceAlarm()];
        $this->embeddedImporter()->apply();
        $before = (array) DB::table('alarm_codes')->first();
        $migration->up();
        $this->assertSame($before, (array) DB::table('alarm_codes')->first());
        $variant = $before;
        unset($variant['id']);
        $variant['section'] = '55/60/65 kW module';
        $variant['source_page'] = 100;
        DB::table('alarm_codes')->insert($variant);
        try {
            $migration->down();
            $this->fail('Rollback must refuse to remove legitimate manual variants.');
        } catch (\Illuminate\Database\QueryException $error) {
            $this->assertSame(2, DB::table('alarm_codes')->count());
            $this->assertSame($before, (array) DB::table('alarm_codes')->where('id', $before['id'])->first());
        }
        // The current index must still reject an identical duplicate after refused rollback.
        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::table('alarm_codes')->insert($variant);
    }

    public function test_data_only_import_needs_no_external_files_and_keeps_document_text_in_database(): void
    {
        unlink($this->package.'/files/2026/manual.pdf');
        $import = $this->embeddedImporter();
        $this->assertTrue($import->preview()['data_only']);
        $import->apply();
        $this->assertTrue($import->apply()['already_imported']);
        $document = \App\Models\Document::first();
        $this->assertSame('', $document->path);
        $this->assertNull($document->url);
        $this->assertFalse($document->can_preview);
        $this->assertSame(2, $document->assignments()->count());
        $this->assertSame([['number' => 4, 'text' => 'P123 source setting']], app(\App\Support\MaintenanceDocumentText::class)->pages($document));
        $this->assertSame(1, DB::table('maintenance_import_records')->where('source_table', 'core_documentpage')->count());
        $this->assertSame([], Storage::allFiles());
        $this->assertSame([], Storage::disk('maintenance_private')->allFiles());
    }

    public function test_pending_document_can_be_uploaded_later_without_losing_assignments_or_using_old_index(): void
    {
        $this->embeddedImporter()->apply();
        $user = User::first();
        foreach (['maintenance.reports.create', 'equipment.documents.edit'] as $name) {
            $user->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']));
        }
        $this->actingAs($user);
        app('url')->forceRootUrl('http://localhost');
        $document = \App\Models\Document::first();
        $this->getJson(route('maintenance-visits.mobile.data'))->assertJsonPath('documents.0.file_available', false)->assertJsonPath('documents.0.url', null);
        $this->get(route('maintenance-visits.mobile.document', $document))->assertNotFound();
        $pdf = new \Mpdf\Mpdf(['tempDir' => storage_path('app/maintenance-pdf-temp')]);
        $pdf->WriteHTML('P456 New uploaded manual.');
        $upload = \Illuminate\Http\UploadedFile::fake()->createWithContent('new.pdf', $pdf->Output('', 'S'));
        $this->put(route('documents.update', $document), ['document_type_id' => $document->document_type_id,
            'name' => $document->name, 'language' => 'en', 'level' => 'unit',
            'equipment_unit_id' => \App\Models\EquipmentUnit::first()->id, 'file' => $upload])->assertRedirect();
        $this->assertSame(2, $document->assignments()->count());
        $this->getJson(route('maintenance-visits.mobile.data'))->assertJsonPath('documents.0.file_available', true);
        $this->get(route('maintenance-visits.mobile.document', $document))->assertOk();
        $this->getJson(route('maintenance-visits.mobile.document-text', $document))->assertJsonPath('pages.0.text', 'P456 New uploaded manual.');
    }

    public function test_switching_to_bundled_seed_does_not_erase_files_from_a_completed_full_import(): void
    {
        $this->importer()->apply();
        $path = \App\Models\Document::first()->path;
        $bytes = Storage::get($path);
        $this->assertTrue($this->embeddedImporter()->apply()['already_imported']);
        $this->assertSame($path, \App\Models\Document::first()->path);
        $this->assertSame($bytes, Storage::get($path));
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
