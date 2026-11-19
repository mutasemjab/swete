<?php

namespace Database\Seeders;

use App\Support\DatabaseSnapshot;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class CurrentDatabaseSeeder extends Seeder
{
    public function run(DatabaseSnapshot $snapshots): void
    {
        $snapshot = $snapshots->read(DatabaseSnapshot::path());
        $restored = $snapshots->restore($snapshot);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info($restored
            ? 'Current database restored and verified (snapshot: '.$snapshot['exported_at'].').'
            : 'This exact database snapshot is already installed; no data was changed.');
    }
}
