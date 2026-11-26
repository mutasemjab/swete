<?php

namespace App\Console\Commands;

use App\Support\MaintenanceBackupImport;
use Illuminate\Console\Command;

class ImportMaintenanceBackup extends Command
{
    protected $signature = 'maintenance:import-backup {package : Private prepared package directory} {--mapping= : Reviewed mapping JSON} {--apply : Apply the additive import; default is read-only preview}';
    protected $description = 'Preview or import a reviewed HVAC maintenance package without restoring the database.';

    public function handle(MaintenanceBackupImport $import): int
    {
        $mapping = $this->option('mapping') ? json_decode(file_get_contents($this->option('mapping')), true, 512, JSON_THROW_ON_ERROR) : [];
        $import->load($this->argument('package'), $mapping);
        $result = $this->option('apply') ? $import->apply() : $import->preview();
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        return empty($result['blockers']) ? self::SUCCESS : self::FAILURE;
    }
}
