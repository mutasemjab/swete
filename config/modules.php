<?php

/**
 * ERP Modules Configuration
 *
 * To add a new module:
 *  1. Add an entry here with its icon, color, route, and sections.
 *     Use translation keys for 'name' and 'description' (e.g. 'settings.module_name').
 *  2. Create app/Http/Controllers/{Module}/{Module}Controller.php
 *     extending ModuleController with protected string $module = 'key'.
 *  3. Add routes to routes/web.php under the auth middleware group.
 *  4. Create views in resources/views/{module}/.
 *  5. Create resources/lang/ar/{module}.php and resources/lang/en/{module}.php.
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
        'gradient'    => 'from-blue-500 to-blue-700',
        'sections'    => [
            [
                'label' => 'accounting.section_operations',
                'items' => [
                    ['label' => 'accounting.nav_invoices',   'route' => 'accounting.invoices.index', 'icon' => 'file-invoice-dollar'],
                  
                ],
            ],
            [
                'label' => 'accounting.section_management',
                'items' => [
                    ['label' => 'accounting.nav_customers',       'route' => 'accounting.customers.index',        'icon' => 'address-book'],
                    ['label' => 'accounting.nav_customer_groups', 'route' => 'accounting.customer-groups.index',  'icon' => 'sitemap'],
                    ['label' => 'accounting.nav_suppliers',       'route' => 'accounting.suppliers.index',        'icon' => 'truck'],
                    ['label' => 'accounting.nav_supplier_groups', 'route' => 'accounting.supplier-groups.index',  'icon' => 'sitemap'],
                    ['label' => 'accounting.nav_invoice_types',   'route' => 'accounting.invoice-types.index',    'icon' => 'tags'],
                ],
            ],
            [
                'label' => 'accounting.section_reports',
                'items' => [
                    ['label' => 'accounting.nav_balance',    'route' => '#', 'icon' => 'scale-balanced'],
               
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
        'gradient'    => 'from-emerald-500 to-emerald-700',
        'sections'    => [
            [
                'label' => 'warehouse.section_operations',
                'items' => [
                    ['label' => 'warehouse.nav_stock_in',          'route' => 'warehouse.vouchers.index',         'icon' => 'box-open',        'params' => ['type' => 'receipt']],
                    ['label' => 'warehouse.nav_stock_out',         'route' => 'warehouse.vouchers.index',         'icon' => 'arrow-up-from-bracket', 'params' => ['type' => 'issue']],
                    ['label' => 'warehouse.nav_transfer',          'route' => 'warehouse.vouchers.index',         'icon' => 'right-left',      'params' => ['type' => 'transfer']],
                    ['label' => 'warehouse.nav_material_requests', 'route' => 'warehouse.material-requests.index', 'icon' => 'clipboard-list'],
                ],
            ],
            [
                'label' => 'warehouse.section_management',
                'items' => [
                    ['label' => 'warehouse.nav_materials',   'route' => 'warehouse.materials.index',   'icon' => 'cube'],
                    ['label' => 'warehouse.nav_categories',  'route' => 'warehouse.categories.index',  'icon' => 'layer-group'],
                    ['label' => 'warehouse.nav_units',       'route' => 'warehouse.units.index',       'icon' => 'ruler'],
                    ['label' => 'warehouse.nav_warehouses',  'route' => 'warehouse.warehouses.index',  'icon' => 'warehouse'],
                ],
            ],
            [
                'label' => 'warehouse.section_reports',
                'items' => [
                    ['label' => 'warehouse.nav_material_report',  'route' => 'warehouse.reports.materials',  'icon' => 'chart-column'],
                    ['label' => 'warehouse.nav_stocktake_report', 'route' => 'warehouse.reports.stocktake',  'icon' => 'clipboard-check'],
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
        'gradient'    => 'from-orange-500 to-orange-700',
        'sections'    => [
            [
                'label' => 'tenders.section_management',
                'items' => [
                    ['label' => 'tenders.nav_list', 'route' => 'tenders.index',  'icon' => 'list'],
                    ['label' => 'tenders.nav_add',  'route' => 'tenders.create', 'icon' => 'plus'],
                ],
            ],
            [
                'label' => 'tenders.section_price_quotes',
                'items' => [
                    ['label' => 'tenders.nav_quotes_list', 'route' => 'price-quotes.index',  'icon' => 'file-invoice'],
                    ['label' => 'tenders.nav_quotes_add',  'route' => 'price-quotes.create', 'icon' => 'plus'],
                ],
            ],
            [
                'label' => 'tenders.section_projects',
                'items' => [
                    ['label' => 'tenders.nav_projects_list', 'route' => 'projects.index', 'icon' => 'diagram-project'],
                ],
            ],
            [
                'label' => 'tenders.section_settings',
                'items' => [
                    ['label' => 'tenders.nav_statuses', 'route' => 'tender-statuses.index', 'icon' => 'list-check'],
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
        'gradient'    => 'from-cyan-500 to-cyan-700',
        'sections'    => [
            [
                'label' => 'external_purchases.section_purchase_requests',
                'items' => [
                    ['label' => 'external_purchases.nav_requests_list', 'route' => 'purchase-requests.index',  'icon' => 'list'],
                    ['label' => 'external_purchases.nav_requests_add',  'route' => 'purchase-requests.create', 'icon' => 'plus'],
                ],
            ],
            [
                'label' => 'external_purchases.section_shipments',
                'items' => [
                    ['label' => 'external_purchases.nav_shipments_list', 'route' => 'shipments.index',  'icon' => 'anchor'],
                    ['label' => 'external_purchases.nav_shipments_add',  'route' => 'shipments.create', 'icon' => 'plus'],
                ],
            ],
            [
                'label' => 'external_purchases.section_shipping_companies',
                'items' => [
                    ['label' => 'external_purchases.nav_shipping_companies_list', 'route' => 'shipping-companies.index',  'icon' => 'ship'],
                    ['label' => 'external_purchases.nav_shipping_companies_add',  'route' => 'shipping-companies.create', 'icon' => 'plus'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // MAINTENANCE MODULE  ← placeholder, service_calls table exists, UI later
    // ──────────────────────────────────────────────────────────────────────────
    'maintenance' => [
        'name'        => 'maintenance.module_name',
        'description' => 'maintenance.module_desc',
        'icon'        => 'screwdriver-wrench',
        'color'       => 'teal',
        'route'       => '#',
        'gradient'    => 'from-teal-500 to-teal-700',
        'sections'    => [
            [
                'label' => 'maintenance.section_service_calls',
                'items' => [
                    ['label' => 'maintenance.nav_service_calls', 'route' => '#', 'icon' => 'screwdriver-wrench'],
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
        'gradient'    => 'from-slate-600 to-slate-800',
        'sections'    => [
            [
                'label' => 'settings.section_management',
                'items' => [
                    ['label' => 'settings.nav_users',        'route' => 'settings.users.index',       'icon' => 'users'],
                    ['label' => 'settings.nav_add_user',     'route' => 'settings.users.create',      'icon' => 'user-plus'],
                    ['label' => 'settings.nav_roles',        'route' => 'settings.roles.index',       'icon' => 'shield-halved'],
                    ['label' => 'settings.nav_permissions',  'route' => 'settings.permissions.index', 'icon' => 'key'],
                ],
            ],
            [
                'label' => 'settings.section_master_data',
                'items' => [
                    ['label' => 'settings.nav_branches',     'route' => 'settings.branches.index',    'icon' => 'code-branch'],
                    ['label' => 'settings.nav_currencies',   'route' => 'settings.currencies.index',  'icon' => 'coins'],
                    ['label' => 'settings.nav_countries',    'route' => 'settings.countries.index',   'icon' => 'earth-americas'],
                ],
            ],
            [
                'label' => 'settings.section_access',
                'items' => [
                    ['label' => 'settings.nav_activity_log',   'route' => 'settings.activity-log.index',   'icon' => 'clock-rotate-left'],
                    ['label' => 'settings.nav_approval_rules', 'route' => 'settings.approval-rules.index', 'icon' => 'user-shield'],
                    ['label' => 'settings.nav_purchase_request_approvers', 'route' => 'settings.purchase-request-approvers.index', 'icon' => 'user-check'],
                ],
            ],
        ],
    ],

];
