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
            'settings.countries.view', 'settings.countries.create', 'settings.countries.edit', 'settings.countries.delete',
            'settings.governorates.view', 'settings.governorates.create', 'settings.governorates.edit', 'settings.governorates.delete',
            'settings.activity_log.view',
            'settings.approval_rules.view', 'settings.approval_rules.edit',
            'settings.purchase_request_approvers.edit',
            'settings.purchase_request_reminder_recipients.edit',
            'settings.maintenance_report_material_approvers.edit',
            'settings.maintenance_notifications.edit',
            'settings.sales_representatives.view', 'settings.sales_representatives.create',
            'settings.sales_representatives.edit', 'settings.sales_representatives.delete',
            'settings.quote_discount_limit.edit',

            // Accounting
            'accounting.view',
            'accounting.customers.create', 'accounting.customers.edit', 'accounting.customers.delete',
            'accounting.suppliers.create', 'accounting.suppliers.edit', 'accounting.suppliers.delete',
            'accounting.customer_groups.create', 'accounting.customer_groups.edit', 'accounting.customer_groups.delete',
            'accounting.supplier_groups.create', 'accounting.supplier_groups.edit', 'accounting.supplier_groups.delete',
            'accounting.invoice_types.create', 'accounting.invoice_types.edit', 'accounting.invoice_types.delete',
            'accounting.invoices.create',
            'accounting.invoices.receipts.delete',
            // Accounting (future — journal/GL not built yet)
            'accounting.journal.create', 'accounting.journal.edit', 'accounting.journal.delete',

            // Clients (customers, suppliers and their groups — moved out of accounting)
            'clients.view',
            'clients.customers.create', 'clients.customers.edit', 'clients.customers.delete',
            'clients.suppliers.create', 'clients.suppliers.edit', 'clients.suppliers.delete',
            'clients.customer_groups.create', 'clients.customer_groups.edit', 'clients.customer_groups.delete',
            'clients.supplier_groups.create', 'clients.supplier_groups.edit', 'clients.supplier_groups.delete',
            'clients.extra_fields.view', 'clients.extra_fields.create', 'clients.extra_fields.edit', 'clients.extra_fields.delete',

            // Warehouse
            'warehouse.view',
            'warehouse.warehouses.create', 'warehouse.warehouses.edit', 'warehouse.warehouses.delete',
            'warehouse.categories.create', 'warehouse.categories.edit', 'warehouse.categories.delete',
            'warehouse.units.create', 'warehouse.units.edit', 'warehouse.units.delete',
            'warehouse.materials.create', 'warehouse.materials.edit', 'warehouse.materials.delete',
            'warehouse.vouchers.create', 'warehouse.vouchers.post',
            'warehouse.material_requests.create', 'warehouse.material_requests.approve', 'warehouse.material_requests.fulfill',
            'warehouse.purchase_orders.create', 'warehouse.purchase_orders.edit',
            'warehouse.purchase_invoices.create',
            'warehouse.reports.view',

            // Tenders
            'tenders.view', 'tenders.create', 'tenders.edit', 'tenders.delete',
            'tenders.price_quotes.create', 'tenders.price_quotes.edit',
            'tenders.price_quotes.assign', 'tenders.price_quotes.convert_to_invoice',
            'tenders.quote_supply_scopes.create', 'tenders.quote_supply_scopes.edit', 'tenders.quote_supply_scopes.delete',
            'tenders.quote_delivery_terms.create', 'tenders.quote_delivery_terms.edit', 'tenders.quote_delivery_terms.delete',
            'tenders.tender_statuses.create', 'tenders.tender_statuses.edit', 'tenders.tender_statuses.delete',
            'tenders.projects.view', 'tenders.projects.convert', 'tenders.projects.edit', 'tenders.projects.delete',
            'tenders.purchase_request_reminders.create',
            'tenders.purchase_request_reminders.edit', 'tenders.purchase_request_reminders.delete',
            'tenders.ciat_discounts.create', 'tenders.ciat_discounts.edit', 'tenders.ciat_discounts.delete',
            'tenders.price_analyses.create', 'tenders.price_analyses.edit', 'tenders.price_analyses.delete',

            // Analyses (standalone module — CIAT discounts + price analyses moved out of tenders)
            'analyses.view',
            'analyses.ciat_discounts.create', 'analyses.ciat_discounts.edit', 'analyses.ciat_discounts.delete',
            'analyses.price_analyses.create', 'analyses.price_analyses.edit', 'analyses.price_analyses.delete',

            // Quotations (standalone module — price quotes for all flows)
            'quotations.view',
            'quotations.price_quotes.create', 'quotations.price_quotes.edit',
            'quotations.price_quotes.assign', 'quotations.price_quotes.convert_to_invoice',

            // Projects (standalone module — moved out of tenders)
            'projects.view', 'projects.edit', 'projects.delete', 'projects.convert',

            // Projects Under Review (new standalone module — approval queue before a converted tender becomes a real project)
            'projects_under_review.view', 'projects_under_review.approve', 'projects_under_review.reject',

            // X8 (new standalone module — global approval queue for tender-linked analysis items)
            'x8.view', 'x8.approve',

            // Split Units (new module — separate quotations for split AC units)
            'split_units.view',
            'split_units.create', 'split_units.edit', 'split_units.delete', 'split_units.print',

            // Agreements (new standalone module — customer/supplier agreement, PDF + note)
            'agreements.view',
            'agreements.create', 'agreements.edit', 'agreements.delete',

            // External Purchases
            'external_purchases.view', 'external_purchases.purchase_requests.create',
            'external_purchases.purchase_requests.edit', 'external_purchases.purchase_requests.delete',
            'external_purchases.purchase_requests.approve', 'external_purchases.purchase_requests.ship',
            'external_purchases.purchase_requests.mark_sent',
            'external_purchases.shipping_companies.create', 'external_purchases.shipping_companies.edit', 'external_purchases.shipping_companies.delete',
            'external_purchases.shipments.create', 'external_purchases.shipments.edit', 'external_purchases.shipments.delete',
            'external_purchases.vendor_email_template.edit',

            // CRM
            'crm.view',
            'crm.appointments.create', 'crm.appointments.edit', 'crm.appointments.delete',
            'crm.appointment_types.create', 'crm.appointment_types.edit', 'crm.appointment_types.delete',

            // Maintenance
            'maintenance.view',
            'maintenance.report_templates.create', 'maintenance.report_templates.edit', 'maintenance.report_templates.delete',
            'maintenance.reports.create', 'maintenance.reports.edit', 'maintenance.reports.delete',
            'maintenance.reports.approve_materials', 'maintenance.reports.convert_to_quote',
            'maintenance.reports.review', 'maintenance.reports.approve',
            'maintenance.contracts.create', 'maintenance.contracts.edit', 'maintenance.contracts.delete',
            'maintenance.contract_payments.create', 'maintenance.contract_payments.delete',
            'maintenance.contract_payments.assign', 'maintenance.contract_payments.convert_to_invoice',
            'maintenance.visit_types.create', 'maintenance.visit_types.edit', 'maintenance.visit_types.delete',
            'maintenance.requests.manage', 'maintenance.visits.manage',
            'maintenance.device_passwords.create', 'maintenance.device_passwords.edit', 'maintenance.device_passwords.delete',
            'maintenance.contract_scheduled_visits.create', 'maintenance.contract_scheduled_visits.delete',
            'maintenance.contract_schedules.create', 'maintenance.contract_schedules.edit', 'maintenance.contract_schedules.delete',

            // Equipment (new standalone module — equipment catalog: categories/subtypes, manufacturers, models, units)
            'equipment.view',
            'equipment.categories.create', 'equipment.categories.edit', 'equipment.categories.delete',
            'equipment.manufacturers.create', 'equipment.manufacturers.edit', 'equipment.manufacturers.delete',
            'equipment.models.create', 'equipment.models.edit', 'equipment.models.delete',
            'equipment.units.create', 'equipment.units.edit', 'equipment.units.delete',
            'equipment.document_types.create', 'equipment.document_types.edit', 'equipment.document_types.delete',
            'equipment.documents.upload', 'equipment.documents.edit', 'equipment.documents.delete',
            'equipment.reading_limits.manage',
            'equipment.control_systems.create', 'equipment.control_systems.edit', 'equipment.control_systems.delete',
            'equipment.alarm_codes.create', 'equipment.alarm_codes.edit', 'equipment.alarm_codes.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Roles ────────────────────────────────────────────────────────────────

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $branchManager = Role::firstOrCreate(['name' => 'branch_manager', 'guard_name' => 'web']);
        $branchManager->syncPermissions([
            'settings.users.view', 'settings.branches.view', 'settings.currencies.view', 'settings.governorates.view',
            'warehouse.view',
        ]);

        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'accounting.view',
            'accounting.invoice_types.create', 'accounting.invoice_types.edit',
            'accounting.invoices.create',
        ]);

        $warehouseOfficer = Role::firstOrCreate(['name' => 'warehouse_officer', 'guard_name' => 'web']);
        $warehouseOfficer->syncPermissions([
            'warehouse.view',
            'warehouse.warehouses.create', 'warehouse.warehouses.edit',
            'warehouse.categories.create', 'warehouse.categories.edit',
            'warehouse.units.create', 'warehouse.units.edit',
            'warehouse.materials.create', 'warehouse.materials.edit',
            'warehouse.vouchers.create', 'warehouse.vouchers.post',
            'warehouse.material_requests.create', 'warehouse.material_requests.fulfill',
            'warehouse.purchase_orders.create', 'warehouse.purchase_orders.edit',
            'warehouse.purchase_invoices.create',
            'warehouse.reports.view',
        ]);

        $maintenanceTechnician = Role::firstOrCreate(['name' => 'maintenance_technician', 'guard_name' => 'web']);
        $maintenanceTechnician->syncPermissions([
            'maintenance.reports.create',
            'equipment.view', 'equipment.documents.upload',
        ]);

        // Explicit lists keep each employee within the requested modules as new permissions
        // are added. Shared lists also keep the maintenance manager aligned with maintenance staff.
        $clients = [
            'clients.view',
            'clients.customers.create', 'clients.customers.edit',
            'clients.suppliers.create', 'clients.suppliers.edit',
            'clients.customer_groups.create', 'clients.customer_groups.edit',
            'clients.supplier_groups.create', 'clients.supplier_groups.edit',
            'clients.extra_fields.view', 'clients.extra_fields.create', 'clients.extra_fields.edit',
        ];
        $quotations = [
            'quotations.view',
            'quotations.price_quotes.create', 'quotations.price_quotes.edit',
        ];
        $maintenance = [
            'maintenance.view',
            'maintenance.report_templates.create', 'maintenance.report_templates.edit',
            'maintenance.reports.create', 'maintenance.reports.edit', 'maintenance.reports.review',
            'maintenance.contracts.create', 'maintenance.contracts.edit',
            'maintenance.contract_payments.create',
            'maintenance.contract_scheduled_visits.create',
            'maintenance.contract_schedules.create', 'maintenance.contract_schedules.edit',
            'maintenance.visit_types.create', 'maintenance.visit_types.edit',
            'maintenance.device_passwords.create', 'maintenance.device_passwords.edit',
        ];
        $equipment = [
            'equipment.view',
            'equipment.categories.create', 'equipment.categories.edit',
            'equipment.manufacturers.create', 'equipment.manufacturers.edit',
            'equipment.models.create', 'equipment.models.edit',
            'equipment.units.create', 'equipment.units.edit',
            'equipment.document_types.create', 'equipment.document_types.edit',
            'equipment.documents.upload', 'equipment.documents.edit',
            'equipment.reading_limits.manage',
            'equipment.control_systems.create', 'equipment.control_systems.edit',
            'equipment.alarm_codes.create', 'equipment.alarm_codes.edit',
        ];

        $projectManager = Role::firstOrCreate(['name' => 'project_manager', 'guard_name' => 'web']);
        $projectManager->syncPermissions([
            ...$clients, ...$quotations,
            'tenders.view', 'tenders.create', 'tenders.edit',
            'tenders.tender_statuses.create', 'tenders.tender_statuses.edit',
            'tenders.quote_supply_scopes.create', 'tenders.quote_supply_scopes.edit',
            'tenders.quote_delivery_terms.create', 'tenders.quote_delivery_terms.edit',
            'tenders.purchase_request_reminders.create', 'tenders.purchase_request_reminders.edit',
            'projects.view', 'projects.edit',
            'x8.view',
        ]);

        Role::firstOrCreate(['name' => 'maintenance_staff', 'guard_name' => 'web'])
            ->syncPermissions([...$maintenance, ...$quotations, 'equipment.view', 'equipment.documents.upload']);

        Role::firstOrCreate(['name' => 'maintenance_manager', 'guard_name' => 'web'])
            ->syncPermissions([
                ...$maintenance, ...$quotations, ...$equipment,
                'maintenance.requests.manage', 'maintenance.visits.manage', 'maintenance.reports.approve',
            ]);

        Role::firstOrCreate(['name' => 'external_purchases_officer', 'guard_name' => 'web'])
            ->syncPermissions([
                ...$clients,
                'projects.view', 'projects.edit',
                'external_purchases.view',
                'external_purchases.purchase_requests.create', 'external_purchases.purchase_requests.edit',
                'external_purchases.shipping_companies.create', 'external_purchases.shipping_companies.edit',
                'external_purchases.shipments.create', 'external_purchases.shipments.edit',
                'external_purchases.vendor_email_template.edit',
            ]);

        // ── Default Super Admin User ──────────────────────────────────────────────

        $admin = User::firstOrCreate(
            ['email' => 'admin@erp.local'],
            [
                'name'     => 'System Administrator',
                'password' => Hash::make('password'),
                'status'   => true,
                'is_maintenance_manager' => true,
            ]
        );

        if (! $admin->hasRole('super-admin')) {
            $admin->assignRole('super-admin');
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command?->info('✓ Permissions and roles seeded.');
        $this->command?->line('  Default login for a new installation: admin@erp.local / password');
    }
}
