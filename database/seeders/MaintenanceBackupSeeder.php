<?php

namespace Database\Seeders;

use App\Support\MaintenanceBackupImport;
use Illuminate\Database\Seeder;

/** Explicit opt-in only; deliberately not called from DatabaseSeeder. */
class MaintenanceBackupSeeder extends Seeder
{
    public function run(MaintenanceBackupImport $import): void
    {
        $import->loadEmbedded(database_path('seeders/data/maintenance-backup.php'));
        $result = $import->apply();
        $this->command?->info($result['already_imported'] ? 'This maintenance backup is already imported; no changes made.' : 'Maintenance data imported. PDFs/photos can be uploaded later; no external package is required.');
    }
}
