<?php

/**
 * ERP Modules Configuration
 *
 * To add a new module:
 *  1. Add an entry here with its icon, color, route, permission, and sections.
 *     Use translation keys for 'name' and 'description' (e.g. 'settings.module_name').
 *  2. Create app/Http/Controllers/{Module}/{Module}Controller.php
 *     extending ModuleController with protected string $module = 'key'.
 *  3. Add routes to routes/web.php under the auth middleware group, each gated
 *     by a 'permission:{module}.{action}' middleware — see [[feedback_permissions_per_section]].
 *  4. Create views in resources/views/{module}/.
 *  5. Create resources/lang/ar/{module}.php and resources/lang/en/{module}.php.
 *  6. Add the matching permission string(s) to database/seeders/PermissionSeeder.php.
 *
 * Every module entry and every nav item below carries a 'permission' key — the
 * exact permission string that gates its route in routes/web.php. DashboardController
 * filters module cards by it and layouts/app.blade.php's sidebar filters nav items by
 * it, so a user only ever sees a link to a page they're actually allowed to open.
 */

return [

    // ──────────────────────────────────────────────────────────────────────────
    // ACCOUNTING MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'accounting' => [
        'name'        => 'accounting.module_name',
        'description' => 'accounting.module_desc',
        'icon'        => 'coins',
        'color'       => 'blue',
        'route'       => 'accounting.invoices.index',
        'permission'  => 'accounting.view',
        'gradient'    => 'from-blue-500 to-blue-700',
        'sections'    => [
            [
                'label' => 'accounting.section_operations',
                'items' => [
                    ['label' => 'accounting.nav_invoices',   'route' => 'accounting.invoices.index', 'icon' => 'file-invoice-dollar', 'permission' => 'accounting.view'],

                ],
            ],
            [
                'label' => 'accounting.section_management',
                'items' => [
                    ['label' => 'accounting.nav_customers',       'route' => 'accounting.customers.index',        'icon' => 'address-book', 'permission' => 'accounting.view'],
                    ['label' => 'accounting.nav_customer_groups', 'route' => 'accounting.customer-groups.index',  'icon' => 'sitemap',      'permission' => 'accounting.view'],
                    ['label' => 'accounting.nav_suppliers',       'route' => 'accounting.suppliers.index',        'icon' => 'truck',        'permission' => 'accounting.view'],
                    ['label' => 'accounting.nav_supplier_groups', 'route' => 'accounting.supplier-groups.index',  'icon' => 'sitemap',      'permission' => 'accounting.view'],
                    ['label' => 'accounting.nav_invoice_types',   'route' => 'accounting.invoice-types.index',    'icon' => 'tags',         'permission' => 'accounting.view'],
                ],
            ],
            [
                'label' => 'accounting.section_reports',
                'items' => [
                    ['label' => 'accounting.nav_balance',    'route' => '#', 'icon' => 'scale-balanced', 'permission' => 'accounting.view'],

                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // WAREHOUSE MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'warehouse' => [
        'name'        => 'warehouse.module_name',
        'description' => 'warehouse.module_desc',
        'icon'        => 'cubes',
        'color'       => 'emerald',
        'route'       => 'warehouse.materials.index',
        'permission'  => 'warehouse.view',
        'gradient'    => 'from-emerald-500 to-emerald-700',
        'sections'    => [
            [
                'label' => 'warehouse.section_operations',
                'items' => [
                    ['label' => 'warehouse.nav_stock_in',          'route' => 'warehouse.vouchers.index',         'icon' => 'box-open',        'params' => ['type' => 'receipt'], 'permission' => 'warehouse.view'],
                    ['label' => 'warehouse.nav_stock_out',         'route' => 'warehouse.vouchers.index',         'icon' => 'arrow-up-from-bracket', 'params' => ['type' => 'issue'], 'permission' => 'warehouse.view'],
                    ['label' => 'warehouse.nav_transfer',          'route' => 'warehouse.vouchers.index',         'icon' => 'right-left',      'params' => ['type' => 'transfer'], 'permission' => 'warehouse.view'],
                    ['label' => 'warehouse.nav_material_requests', 'route' => 'warehouse.material-requests.index', 'icon' => 'clipboard-list', 'permission' => 'warehouse.view'],
                ],
            ],
            [
                'label' => 'warehouse.section_management',
                'items' => [
                    ['label' => 'warehouse.nav_materials',   'route' => 'warehouse.materials.index',   'icon' => 'cube',      'permission' => 'warehouse.view'],
                    ['label' => 'warehouse.nav_draft_materials', 'route' => 'warehouse.materials.drafts', 'icon' => 'hourglass-half', 'permission' => 'warehouse.view'],
                    ['label' => 'warehouse.nav_categories',  'route' => 'warehouse.categories.index',  'icon' => 'layer-group', 'permission' => 'warehouse.view'],
                    ['label' => 'warehouse.nav_units',       'route' => 'warehouse.units.index',       'icon' => 'ruler',     'permission' => 'warehouse.view'],
                    ['label' => 'warehouse.nav_warehouses',  'route' => 'warehouse.warehouses.index',  'icon' => 'warehouse', 'permission' => 'warehouse.view'],
                ],
            ],
            [
                'label' => 'warehouse.section_reports',
                'items' => [
                    ['label' => 'warehouse.nav_material_report',  'route' => 'warehouse.reports.materials',  'icon' => 'chart-column',     'permission' => 'warehouse.reports.view'],
                    ['label' => 'warehouse.nav_stocktake_report', 'route' => 'warehouse.reports.stocktake',  'icon' => 'clipboard-check',  'permission' => 'warehouse.reports.view'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // TENDERS MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'tenders' => [
        'name'        => 'tenders.module_name',
        'description' => 'tenders.module_desc',
        'icon'        => 'gavel',
        'color'       => 'orange',
        'route'       => 'tenders.index',
        'permission'  => 'tenders.view',
        'gradient'    => 'from-orange-500 to-orange-700',
        'sections'    => [
            [
                'label' => 'tenders.section_management',
                'items' => [
                    ['label' => 'tenders.nav_list', 'route' => 'tenders.index',  'icon' => 'list', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_add',  'route' => 'tenders.create', 'icon' => 'plus', 'permission' => 'tenders.create'],
                ],
            ],
            [
                'label' => 'tenders.section_price_quotes',
                'items' => [
                    ['label' => 'tenders.nav_quotes_list', 'route' => 'price-quotes.index',  'icon' => 'file-invoice', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_quotes_add',  'route' => 'price-quotes.create', 'icon' => 'plus',         'permission' => 'tenders.price_quotes.create'],
                ],
            ],
            [
                'label' => 'tenders.section_ciat_discounts',
                'items' => [
                    ['label' => 'tenders.nav_ciat_discounts_list', 'route' => 'ciat-discounts.index',  'icon' => 'percent', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_ciat_discounts_add',  'route' => 'ciat-discounts.create', 'icon' => 'plus',    'permission' => 'tenders.ciat_discounts.create'],
                ],
            ],
            [
                'label' => 'tenders.section_price_analysis',
                'items' => [
                    ['label' => 'tenders.nav_price_analyses_list', 'route' => 'price-analyses.index',  'icon' => 'chart-line', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_price_analyses_add',  'route' => 'price-analyses.create', 'icon' => 'plus',       'permission' => 'tenders.price_analyses.create'],
                ],
            ],
            [
                'label' => 'tenders.section_projects',
                'items' => [
                    ['label' => 'tenders.nav_projects_list', 'route' => 'projects.index', 'icon' => 'diagram-project', 'permission' => 'tenders.projects.view'],
                ],
            ],
            [
                'label' => 'tenders.section_reminders',
                'items' => [
                    ['label' => 'tenders.nav_reminders_list', 'route' => 'purchase-request-reminders.index',  'icon' => 'bell', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_reminders_add',  'route' => 'purchase-request-reminders.create', 'icon' => 'plus', 'permission' => 'tenders.purchase_request_reminders.create'],
                ],
            ],
            [
                'label' => 'tenders.section_settings',
                'items' => [
                    ['label' => 'tenders.nav_statuses', 'route' => 'tender-statuses.index', 'icon' => 'list-check', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_quote_supply_scopes', 'route' => 'quote-supply-scopes.index', 'icon' => 'boxes-stacked', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_quote_delivery_terms', 'route' => 'quote-delivery-terms.index', 'icon' => 'truck-fast', 'permission' => 'tenders.view'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // EXTERNAL PURCHASES MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'external_purchases' => [
        'name'        => 'external_purchases.module_name',
        'description' => 'external_purchases.module_desc',
        'icon'        => 'truck-ramp-box',
        'color'       => 'cyan',
        'route'       => 'purchase-requests.index',
        'permission'  => 'external_purchases.view',
        'gradient'    => 'from-cyan-500 to-cyan-700',
        'sections'    => [
            [
                'label' => 'external_purchases.section_purchase_requests',
                'items' => [
                    ['label' => 'external_purchases.nav_requests_list', 'route' => 'purchase-requests.index',  'icon' => 'list', 'permission' => 'external_purchases.view'],
                    ['label' => 'external_purchases.nav_requests_add',  'route' => 'purchase-requests.create', 'icon' => 'plus', 'permission' => 'external_purchases.purchase_requests.create'],
                ],
            ],
            [
                'label' => 'external_purchases.section_shipments',
                'items' => [
                    ['label' => 'external_purchases.nav_shipments_list', 'route' => 'shipments.index',  'icon' => 'anchor', 'permission' => 'external_purchases.view'],
                    ['label' => 'external_purchases.nav_shipments_add',  'route' => 'shipments.create', 'icon' => 'plus',   'permission' => 'external_purchases.shipments.create'],
                ],
            ],
            [
                'label' => 'external_purchases.section_shipping_companies',
                'items' => [
                    ['label' => 'external_purchases.nav_shipping_companies_list', 'route' => 'shipping-companies.index',  'icon' => 'ship', 'permission' => 'external_purchases.view'],
                    ['label' => 'external_purchases.nav_shipping_companies_add',  'route' => 'shipping-companies.create', 'icon' => 'plus', 'permission' => 'external_purchases.shipping_companies.create'],
                ],
            ],
            [
                'label' => 'external_purchases.section_vendor_email_template',
                'items' => [
                    ['label' => 'external_purchases.nav_vendor_email_template', 'route' => 'vendor-email-template.index', 'icon' => 'envelope-open-text', 'permission' => 'external_purchases.vendor_email_template.edit'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // CRM MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'crm' => [
        'name'        => 'crm.module_name',
        'description' => 'crm.module_desc',
        'icon'        => 'handshake',
        'color'       => 'rose',
        'route'       => 'appointments.index',
        'permission'  => 'crm.view',
        'gradient'    => 'from-rose-500 to-rose-700',
        'sections'    => [
            [
                'label' => 'crm.section_appointments',
                'items' => [
                    ['label' => 'crm.nav_appointments_list', 'route' => 'appointments.index',  'icon' => 'calendar-days', 'permission' => 'crm.view'],
                    ['label' => 'crm.nav_appointments_add',  'route' => 'appointments.create', 'icon' => 'plus',          'permission' => 'crm.appointments.create'],
                ],
            ],
            [
                'label' => 'crm.section_settings',
                'items' => [
                    ['label' => 'crm.nav_appointment_types', 'route' => 'appointment-types.index', 'icon' => 'tags', 'permission' => 'crm.view'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // MAINTENANCE MODULE  ← price quotes reuses the Tenders resource as-is; service_calls UI later
    // ──────────────────────────────────────────────────────────────────────────
    'maintenance' => [
        'name'        => 'maintenance.module_name',
        'description' => 'maintenance.module_desc',
        'icon'        => 'screwdriver-wrench',
        'color'       => 'teal',
        'route'       => 'maintenance-reports.index',
        'permission'  => 'maintenance.view',
        'gradient'    => 'from-teal-500 to-teal-700',
        'sections'    => [
            [
                'label' => 'maintenance.section_price_quotes',
                'items' => [
                    ['label' => 'tenders.nav_quotes_list', 'route' => 'price-quotes.index',  'icon' => 'file-invoice', 'permission' => 'tenders.view'],
                    ['label' => 'tenders.nav_quotes_add',  'route' => 'price-quotes.create', 'icon' => 'plus',         'permission' => 'tenders.price_quotes.create'],
                ],
            ],
            [
                'label' => 'maintenance.section_reports',
                'items' => [
                    ['label' => 'maintenance.nav_reports_list',   'route' => 'maintenance-reports.index',  'icon' => 'clipboard-list', 'permission' => 'maintenance.view'],
                    ['label' => 'maintenance.nav_reports_add',    'route' => 'maintenance-reports.create', 'icon' => 'plus',           'permission' => 'maintenance.reports.create'],
                    ['label' => 'maintenance.nav_reports_search', 'route' => 'maintenance-reports.search', 'icon' => 'magnifying-glass', 'permission' => 'maintenance.view'],
                    ['label' => 'maintenance.nav_report_templates', 'route' => 'report-templates.index',   'icon' => 'sliders',        'permission' => 'maintenance.view'],
                ],
            ],
            [
                'label' => 'maintenance.section_contracts',
                'items' => [
                    ['label' => 'maintenance.nav_contracts_list',  'route' => 'maintenance-contracts.index',  'icon' => 'file-contract', 'permission' => 'maintenance.view'],
                    ['label' => 'maintenance.nav_contracts_add',   'route' => 'maintenance-contracts.create', 'icon' => 'plus',          'permission' => 'maintenance.contracts.create'],
                    ['label' => 'maintenance.nav_contract_payments', 'route' => 'contract-payments.index',    'icon' => 'money-check-dollar', 'permission' => 'maintenance.view'],
                ],
            ],
            [
                'label' => 'maintenance.section_service_calls',
                'items' => [
                    ['label' => 'maintenance.nav_service_calls', 'route' => '#', 'icon' => 'screwdriver-wrench', 'permission' => 'maintenance.view'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // SETTINGS MODULE  ← fully implemented
    // ──────────────────────────────────────────────────────────────────────────
    'settings' => [
        'name'        => 'settings.module_name',
        'description' => 'settings.module_desc',
        'icon'        => 'sliders',
        'color'       => 'slate',
        'route'       => 'settings.users.index',
        'permission'  => 'settings.users.view',
        'gradient'    => 'from-slate-600 to-slate-800',
        'sections'    => [
            [
                'label' => 'settings.section_management',
                'items' => [
                    ['label' => 'settings.nav_users',        'route' => 'settings.users.index',       'icon' => 'users',          'permission' => 'settings.users.view'],
                    ['label' => 'settings.nav_add_user',     'route' => 'settings.users.create',      'icon' => 'user-plus',      'permission' => 'settings.users.create'],
                    ['label' => 'settings.nav_roles',        'route' => 'settings.roles.index',       'icon' => 'shield-halved',  'permission' => 'settings.roles.view'],
                    ['label' => 'settings.nav_permissions',  'route' => 'settings.permissions.index', 'icon' => 'key',            'permission' => 'settings.permissions.view'],
                ],
            ],
            [
                'label' => 'settings.section_master_data',
                'items' => [
                    ['label' => 'settings.nav_branches',     'route' => 'settings.branches.index',    'icon' => 'code-branch',       'permission' => 'settings.branches.view'],
                    ['label' => 'settings.nav_currencies',   'route' => 'settings.currencies.index',  'icon' => 'coins',              'permission' => 'settings.currencies.view'],
                    ['label' => 'settings.nav_countries',    'route' => 'settings.countries.index',   'icon' => 'earth-americas',     'permission' => 'settings.countries.view'],
                    ['label' => 'settings.nav_governorates', 'route' => 'settings.governorates.index', 'icon' => 'map-location-dot',  'permission' => 'settings.governorates.view'],
                    ['label' => 'settings.nav_sales_representatives', 'route' => 'settings.sales-representatives.index', 'icon' => 'user-tie', 'permission' => 'settings.sales_representatives.view'],
                ],
            ],
            [
                'label' => 'settings.section_access',
                'items' => [
                    ['label' => 'settings.nav_activity_log',   'route' => 'settings.activity-log.index',   'icon' => 'clock-rotate-left', 'permission' => 'settings.activity_log.view'],
                    ['label' => 'settings.nav_approval_rules', 'route' => 'settings.approval-rules.index', 'icon' => 'user-shield',       'permission' => 'settings.approval_rules.view'],
                    ['label' => 'settings.nav_purchase_request_approvers', 'route' => 'settings.purchase-request-approvers.index', 'icon' => 'user-check', 'permission' => 'settings.purchase_request_approvers.edit'],
                    ['label' => 'settings.nav_purchase_request_reminder_recipients', 'route' => 'settings.purchase-request-reminder-recipients.index', 'icon' => 'bell', 'permission' => 'settings.purchase_request_reminder_recipients.edit'],
                    ['label' => 'settings.nav_maintenance_report_material_approvers', 'route' => 'settings.maintenance-report-material-approvers.index', 'icon' => 'user-check', 'permission' => 'settings.maintenance_report_material_approvers.edit'],
                ],
            ],
        ],
    ],

];
