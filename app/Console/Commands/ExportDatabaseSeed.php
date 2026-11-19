<?php

namespace App\Console\Commands;

use App\Support\DatabaseSnapshot;
use Illuminate\Console\Command;

class ExportDatabaseSeed extends Command
{
    protected $signature = 'database:export-seed';

    protected $description = 'Capture the current database for CurrentDatabaseSeeder (data only; no login tokens or queued jobs).';

    public function handle(DatabaseSnapshot $snapshots): int
    {
        $snapshot = $snapshots->capture();
        $snapshots->write($snapshot, DatabaseSnapshot::path());
        $this->info('Database snapshot saved: '.DatabaseSnapshot::path());
        $this->table(['Table', 'Rows'], collect($snapshot['tables'])
            ->filter(fn ($data) => count($data['rows']) > 0)
            ->map(fn ($data, $table) => [$table, count($data['rows'])])->values()->all());

        return self::SUCCESS;
    }
}
