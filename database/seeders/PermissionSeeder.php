<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Settings
            'settings.users.view',     'settings.users.create',     'settings.users.edit',     'settings.users.delete',
            'settings.roles.view',     'settings.roles.create',     'settings.roles.edit',     'settings.roles.delete',
            'settings.permissions.view',
            'settings.branches.view',  'settings.branches.create',  'settings.branches.edit',  'settings.branches.delete',
            'settings.currencies.view','settings.currencies.create','settings.currencies.edit','settings.currencies.delete',

            // Accounting
            'accounting.view',
            'accounting.customers.create', 'accounting.customers.edit', 'accounting.customers.delete',
            'accounting.suppliers.create', 'accounting.suppliers.edit', 'accounting.suppliers.delete',
            'accounting.customer_groups.create', 'accounting.customer_groups.edit', 'accounting.customer_groups.delete',
            'accounting.supplier_groups.create', 'accounting.supplier_groups.edit', 'accounting.supplier_groups.delete',
            'accounting.invoice_types.create', 'accounting.invoice_types.edit', 'accounting.invoice_types.delete',
            'accounting.invoices.create',
            // Accounting (future — journal/GL not built yet)
            'accounting.journal.create', 'accounting.journal.edit', 'accounting.journal.delete',

            // Warehouse
            'warehouse.view',
            'warehouse.warehouses.create', 'warehouse.warehouses.edit', 'warehouse.warehouses.delete',
            'warehouse.categories.create', 'warehouse.categories.edit', 'warehouse.categories.delete',
            'warehouse.units.create', 'warehouse.units.edit', 'warehouse.units.delete',
            'warehouse.materials.create', 'warehouse.materials.edit', 'warehouse.materials.delete',
            'warehouse.vouchers.create', 'warehouse.vouchers.post',
            'warehouse.material_requests.create', 'warehouse.material_requests.approve', 'warehouse.material_requests.fulfill',
            'warehouse.reports.view',

            // Sales (future)
            'sales.view', 'sales.orders.create', 'sales.orders.edit', 'sales.orders.delete',
            'sales.customers.create', 'sales.customers.edit',

            // Purchases (future)
            'purchases.view', 'purchases.orders.create', 'purchases.orders.edit',
            'purchases.vendors.create', 'purchases.vendors.edit',

            // HR (future)
            'hr.view', 'hr.employees.create', 'hr.employees.edit', 'hr.employees.delete',
            'hr.payroll.view', 'hr.payroll.run',

            // Tenders
            'tenders.view', 'tenders.create', 'tenders.edit', 'tenders.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Roles ────────────────────────────────────────────────────────────────

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $branchManager = Role::firstOrCreate(['name' => 'branch_manager', 'guard_name' => 'web']);
        $branchManager->syncPermissions([
            'settings.users.view', 'settings.branches.view', 'settings.currencies.view',
            'warehouse.view', 'sales.view', 'purchases.view',
        ]);

        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'accounting.view',
            'accounting.customers.create', 'accounting.customers.edit',
            'accounting.suppliers.create', 'accounting.suppliers.edit',
            'accounting.customer_groups.create', 'accounting.customer_groups.edit',
            'accounting.supplier_groups.create', 'accounting.supplier_groups.edit',
            'accounting.invoice_types.create', 'accounting.invoice_types.edit',
            'accounting.invoices.create',
            'settings.currencies.view',
        ]);

        $warehouseOfficer = Role::firstOrCreate(['name' => 'warehouse_officer', 'guard_name' => 'web']);
        $warehouseOfficer->syncPermissions([
            'warehouse.view',
            'warehouse.categories.create', 'warehouse.categories.edit',
            'warehouse.units.create', 'warehouse.units.edit',
            'warehouse.materials.create', 'warehouse.materials.edit',
            'warehouse.vouchers.create', 'warehouse.vouchers.post',
            'warehouse.material_requests.create', 'warehouse.material_requests.fulfill',
            'warehouse.reports.view',
        ]);

        // ── Default Super Admin User ──────────────────────────────────────────────

        $admin = User::firstOrCreate(
            ['email' => 'admin@erp.local'],
            [
                'name'     => 'System Administrator',
                'password' => Hash::make('password'),
                'status'   => true,
            ]
        );

        if (! $admin->hasRole('super-admin')) {
            $admin->assignRole('super-admin');
        }

        $this->command->info('✓ Permissions and roles seeded.');
        $this->command->line('  Login: admin@erp.local / password');
    }
}
