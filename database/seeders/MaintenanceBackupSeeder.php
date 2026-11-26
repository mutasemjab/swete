<?php

namespace Database\Seeders;

use App\Support\MaintenanceBackupImport;
use Illuminate\Database\Seeder;
use RuntimeException;

/** Explicit opt-in only; deliberately not called from DatabaseSeeder. */
class MaintenanceBackupSeeder extends Seeder
{
    public function run(MaintenanceBackupImport $import): void
    {
        $package = getenv('MAINTENANCE_IMPORT_PACKAGE');
        $mapping = getenv('MAINTENANCE_IMPORT_MAPPING');
        if (! $package || ! $mapping) throw new RuntimeException('Set MAINTENANCE_IMPORT_PACKAGE and MAINTENANCE_IMPORT_MAPPING to the reviewed private files.');
        $import->load($package, json_decode(file_get_contents($mapping), true, 512, JSON_THROW_ON_ERROR));
        $result = $import->apply();
        $this->command?->info($result['already_imported'] ? 'This maintenance backup is already imported; no changes made.' : 'Maintenance backup imported; existing records were not overwritten.');
    }
}
