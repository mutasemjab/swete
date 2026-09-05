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
                    ['label' => 'accounting.nav_journal',    'route' => '#', 'icon' => 'book-open'],
                    ['label' => 'accounting.nav_payments',   'route' => '#', 'icon' => 'money-bill-transfer'],
                    ['label' => 'accounting.nav_cash',       'route' => '#', 'icon' => 'money-bill'],
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
                    ['label' => 'accounting.nav_pnl',        'route' => '#', 'icon' => 'chart-line'],
                    ['label' => 'accounting.nav_tax',        'route' => '#', 'icon' => 'percent'],
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
    // SALES MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'sales' => [
        'name'        => 'sales.module_name',
        'description' => 'sales.module_desc',
        'icon'        => 'chart-line',
        'color'       => 'amber',
        'route'       => '#',
        'gradient'    => 'from-amber-500 to-orange-600',
        'sections'    => [
            [
                'label' => 'sales.section_operations',
                'items' => [
                    ['label' => 'sales.nav_orders',          'route' => '#', 'icon' => 'clipboard-list'],
                    ['label' => 'sales.nav_quotes',          'route' => '#', 'icon' => 'file-lines'],
                    ['label' => 'sales.nav_invoices',        'route' => '#', 'icon' => 'file-invoice-dollar'],
                ],
            ],
            [
                'label' => 'sales.section_management',
                'items' => [
                    ['label' => 'sales.nav_customers',       'route' => '#', 'icon' => 'address-book'],
                    ['label' => 'sales.nav_pricelist',       'route' => '#', 'icon' => 'tags'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // PURCHASES MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'purchases' => [
        'name'        => 'purchases.module_name',
        'description' => 'purchases.module_desc',
        'icon'        => 'cart-shopping',
        'color'       => 'violet',
        'route'       => '#',
        'gradient'    => 'from-violet-500 to-purple-700',
        'sections'    => [
            [
                'label' => 'purchases.section_operations',
                'items' => [
                    ['label' => 'purchases.nav_orders',      'route' => '#', 'icon' => 'cart-shopping'],
                    ['label' => 'purchases.nav_rfq',         'route' => '#', 'icon' => 'file-pen'],
                    ['label' => 'purchases.nav_invoices',    'route' => '#', 'icon' => 'receipt'],
                ],
            ],
            [
                'label' => 'purchases.section_management',
                'items' => [
                    ['label' => 'purchases.nav_vendors',     'route' => '#', 'icon' => 'truck'],
                ],
            ],
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    // HR MODULE
    // ──────────────────────────────────────────────────────────────────────────
    'hr' => [
        'name'        => 'hr.module_name',
        'description' => 'hr.module_desc',
        'icon'        => 'people-group',
        'color'       => 'rose',
        'route'       => '#',
        'gradient'    => 'from-rose-500 to-pink-700',
        'sections'    => [
            [
                'label' => 'hr.section_management',
                'items' => [
                    ['label' => 'hr.nav_employees',          'route' => '#', 'icon' => 'id-card'],
                    ['label' => 'hr.nav_leaves',             'route' => '#', 'icon' => 'umbrella-beach'],
                    ['label' => 'hr.nav_attendance',         'route' => '#', 'icon' => 'fingerprint'],
                ],
            ],
            [
                'label' => 'hr.section_payroll',
                'items' => [
                    ['label' => 'hr.nav_payroll',            'route' => '#', 'icon' => 'money-check'],
                    ['label' => 'hr.nav_payslips',           'route' => '#', 'icon' => 'file-lines'],
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
                ],
            ],
            [
                'label' => 'settings.section_access',
                'items' => [
                    ['label' => 'settings.nav_activity_log', 'route' => 'settings.activity-log.index', 'icon' => 'clock-rotate-left'],
                ],
            ],
        ],
    ],

];
