<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Deploy the captured database as a whole, preserving IDs, stock history and access.
        // Individual reference/user seeders remain available for explicit later updates.
        $this->call(CurrentDatabaseSeeder::class);
    }
}
