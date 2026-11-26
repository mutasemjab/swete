<?php

$app = require __DIR__.'/bootstrap.php';
$path = config('database.connections.sqlite.database');
if (file_exists($path)) throw new RuntimeException('Use a new temporary directory for each browser run.');
touch($path);
\Illuminate\Support\Facades\Artisan::call('migrate', ['--database' => 'sqlite', '--force' => true]);
$permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'maintenance.reports.create', 'guard_name' => 'web']);
$role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'maintenance_technician', 'guard_name' => 'web']);
$role->givePermissionTo($permission);
$user = \App\Models\User::create(['name' => 'Browser Technician', 'email' => 'browser@example.test', 'password' => bcrypt('local-browser-test'), 'status' => true]);
$user->assignRole($role);
\App\Models\Customer::create(['code' => 'BROWSER-CLIENT', 'name' => 'عميل اختبار المتصفح']);
$type = \App\Models\DocumentType::create(['name' => 'Manual']);
$pdf = new \Mpdf\Mpdf(['tempDir' => config('filesystems.disks.local.root').'/pdf-temp']);
$pdf->WriteHTML('P123 - Browser test controller parameter, source page one.');
$bytes = $pdf->Output('', 'S');
\Illuminate\Support\Facades\Storage::put('browser-manual.pdf', $bytes);
$document = \App\Models\Document::create(['document_type_id' => $type->id, 'name' => 'Browser manual', 'language' => 'en',
    'path' => 'browser-manual.pdf', 'original_name' => 'browser-manual.pdf', 'size' => strlen($bytes), 'uploaded_by' => $user->id]);
\App\Models\DocumentAssignment::create(['document_id' => $document->id, 'level' => 'all', 'created_by' => $user->id]);
echo "Isolated browser database ready.\n";
