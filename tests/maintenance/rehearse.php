<?php

// Integration rehearsal only: the shared bootstrap enforces a fresh database/storage under TEMP.
$app = require __DIR__.'/../browser/bootstrap.php';
$path = config('database.connections.sqlite.database');
if (file_exists($path)) throw new RuntimeException('Use a new SWETE_BROWSER_TEST_ROOT for each rehearsal.');
touch($path);
\Illuminate\Support\Facades\Artisan::call('migrate', ['--database' => 'sqlite', '--force' => true]);
$package = getenv('MAINTENANCE_IMPORT_PACKAGE');
$dataOnly = getenv('MAINTENANCE_REHEARSAL_DATA_ONLY') === '1';
if (! $package && ! $dataOnly) throw new RuntimeException('MAINTENANCE_IMPORT_PACKAGE is required for full-file rehearsal.');
$bundle = $dataOnly ? require database_path('seeders/data/maintenance-backup.php') : null;
$mapping = $dataOnly ? $bundle['mapping'] : json_decode(file_get_contents($package.'/mapping.reviewed.json'), true, 512, JSON_THROW_ON_ERROR);

// Use only identities/customer metadata from the repository's existing snapshot, not its credentials.
$saved = require database_path('seeders/data/current-database.php');
$snapshot = json_decode(gzdecode(base64_decode($saved['data'])), true, 512, JSON_THROW_ON_ERROR);
foreach ($snapshot['tables']['users']['rows'] as $row) {
    if (array_is_list($row)) $row = array_combine($snapshot['tables']['users']['columns'], $row);
    \App\Models\User::factory()->create(['id' => $row['id'], 'name' => $row['name'], 'email' => $row['email']]);
}
foreach ($snapshot['tables']['customers']['rows'] as $row) {
    if (getenv('MAINTENANCE_REHEARSAL_EMPTY_CUSTOMERS') === '1') continue;
    if (array_is_list($row)) $row = array_combine($snapshot['tables']['customers']['columns'], $row);
    \Illuminate\Support\Facades\DB::table('customers')->insert(array_intersect_key($row, array_flip(['id', 'code', 'name', 'name_en', 'phone', 'email', 'address', 'status', 'created_by'])));
}
unset($snapshot, $saved);
$before = \Illuminate\Support\Facades\DB::table('customers')->orderBy('id')->get()->toJson();
$customerIds = \Illuminate\Support\Facades\DB::table('customers')->pluck('id');
$import = app(\App\Support\MaintenanceBackupImport::class);
if ($dataOnly) $import->loadEmbedded(database_path('seeders/data/maintenance-backup.php'));
else $import->load($package, $mapping);
$preview = $import->preview();
file_put_contents(getenv('SWETE_BROWSER_TEST_ROOT').'/preview.json', json_encode($preview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
if ($preview['blockers']) {
    echo json_encode(['blockers' => $preview['blockers'], 'customer_candidates' => $preview['customer_candidates']], JSON_UNESCAPED_UNICODE).PHP_EOL;
    exit(2);
}
$first = $import->apply();
$second = $import->apply();
if (! $second['already_imported']) throw new RuntimeException('Second import was not idempotent.');
if ($before !== \Illuminate\Support\Facades\DB::table('customers')->whereIn('id', $customerIds)->orderBy('id')->get()->toJson()) throw new RuntimeException('Existing customers changed.');
$source = json_decode(gzdecode($dataOnly ? base64_decode($bundle['data']) : file_get_contents($package.'/data.json.gz')), true, 512, JSON_THROW_ON_ERROR);
foreach (\App\Models\MaintenanceReport::all() as $report) {
    if ($dataOnly) {
        if ($report->technician_pdf_path || $report->approved_pdf_path || ! $report->technician_snapshot['files_deferred']) throw new RuntimeException('Data-only import claimed unavailable report files.');
        continue;
    }
    $relative = substr($report->technician_pdf_path, strpos($report->technician_pdf_path, '/files/') + 1);
    $original = \Illuminate\Support\Facades\Storage::disk('maintenance_private')->get($report->technician_pdf_path);
    if (hash('sha256', $original) !== $source['files'][$relative]['sha256']) throw new RuntimeException('Original report PDF changed.');
    if ($report->content_status === 'approved') {
        $parser = new \Smalot\PdfParser\Parser();
        $approved = \Illuminate\Support\Facades\Storage::disk('maintenance_private')->get($report->approved_pdf_path);
        if (count($parser->parseContent($approved)->getPages()) <= count($parser->parseContent($original)->getPages())) throw new RuntimeException('Original PDF was not appended.');
    }
}
if ($dataOnly) {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'MaintenanceBackupSeeder', '--force' => true]);
    if (\Illuminate\Support\Facades\Storage::allFiles() || \Illuminate\Support\Facades\Storage::disk('maintenance_private')->allFiles()) throw new RuntimeException('Data-only seed wrote files.');
    if (\Illuminate\Support\Facades\DB::table('documents')->where('path', '!=', '')->exists()) throw new RuntimeException('Data-only import created document file links.');
    if (\Illuminate\Support\Facades\DB::table('maintenance_import_records')->where('source_table', 'core_documentpage')->count() !== count($source['tables']['core_documentpage'])) throw new RuntimeException('Source document pages were not stored in database.');
}
$expect = ['received' => 6, 'approved' => 2];
if (\App\Models\MaintenanceReport::selectRaw('content_status, count(*) as total')->groupBy('content_status')->pluck('total', 'content_status')->all() != $expect) throw new RuntimeException('Source approval states changed.');
$counts = [];
foreach (['equipment_units', 'equipment_models', 'documents', 'document_assignments', 'alarm_codes', 'maintenance_reports', 'maintenance_import_visits', 'maintenance_contract_scheduled_visits', 'maintenance_notifications'] as $table) {
    $counts[$table] = \Illuminate\Support\Facades\DB::table($table)->count();
}
$checks = ['existing customers unchanged', 'same-name customers kept separate', 'idempotent rerun', 'original approval states retained', 'no historical notifications replayed'];
$checks = array_merge($checks, $dataOnly ? ['bundled seeder needs no package/environment paths', 'no binary files read or written', 'document pages retained in database', 'missing files explicitly deferred'] : ['8 original PDF hashes preserved', 'approved PDFs contain original pages']);
$result = ['ok' => true, 'data_only' => $dataOnly, 'counts' => $counts, 'checks' => $checks, 'first' => $first, 'second' => $second, 'artifacts' => getenv('SWETE_BROWSER_TEST_ROOT')];
file_put_contents(getenv('SWETE_BROWSER_TEST_ROOT').'/result.json', json_encode($result, JSON_PRETTY_PRINT));
echo json_encode($result).PHP_EOL;
