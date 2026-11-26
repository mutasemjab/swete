<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'settings.maintenance_notifications.edit', 'guard_name' => 'web']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        \Spatie\Permission\Models\Permission::where('name', 'settings.maintenance_notifications.edit')->where('guard_name', 'web')->delete();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
