<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\BranchController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\CountryController;
use App\Http\Controllers\Settings\GovernorateController;
use App\Http\Controllers\Settings\ActivityLogController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Warehouse\MaterialCategoryController;
use App\Http\Controllers\Warehouse\UnitController;
use App\Http\Controllers\Warehouse\MaterialController;
use App\Http\Controllers\Warehouse\StockVoucherController;
use App\Http\Controllers\Warehouse\MaterialRequestController;
use App\Http\Controllers\Warehouse\WarehouseReportController;
use App\Http\Controllers\Accounting\CustomerController;
use App\Http\Controllers\Accounting\CustomerGroupController;
use App\Http\Controllers\Accounting\SupplierController;
use App\Http\Controllers\Accounting\SupplierGroupController;
use App\Http\Controllers\Accounting\InvoiceTypeController;
use App\Http\Controllers\Accounting\InvoiceController;
use App\Http\Controllers\Accounting\InvoiceReceiptController;
use App\Http\Controllers\Tenders\TenderController;
use App\Http\Controllers\Tenders\TenderStatusController;
use App\Http\Controllers\Tenders\PriceQuoteController;
use App\Http\Controllers\Tenders\QuoteSupplyScopeController;
use App\Http\Controllers\Tenders\QuoteDeliveryTermController;
use App\Http\Controllers\Tenders\CiatDiscountController;
use App\Http\Controllers\Tenders\PriceAnalysisController;
use App\Http\Controllers\Tenders\ProjectController;
use App\Http\Controllers\Tenders\ProjectAttachmentController;
use App\Http\Controllers\Tenders\ProjectDeviceController;
use App\Http\Controllers\Tenders\ProjectItemAssignmentController;
use App\Http\Controllers\Tenders\ProjectQuestionController;
use App\Http\Controllers\Tenders\ProjectReviewController;
use App\Http\Controllers\Tenders\PurchaseRequestReminderController;
use App\Http\Controllers\Crm\AppointmentController;
use App\Http\Controllers\Crm\AppointmentTypeController;
use App\Http\Controllers\Maintenance\ReportTemplateController;
use App\Http\Controllers\Maintenance\ReportController as MaintenanceReportController;
use App\Http\Controllers\Maintenance\ContractController as MaintenanceContractController;
use App\Http\Controllers\Maintenance\ContractPaymentController;
use App\Http\Controllers\Maintenance\ContractScheduledVisitController;
use App\Http\Controllers\Maintenance\MobileVisitController;
use App\Http\Controllers\Maintenance\MaintenanceVisitTypeController;
use App\Http\Controllers\Maintenance\MaintenanceRequestController;
use App\Http\Controllers\Maintenance\MaintenanceVisitController;
use App\Http\Controllers\Maintenance\DevicePasswordController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\ExternalPurchases\PurchaseRequestController;
use App\Http\Controllers\ExternalPurchases\PurchaseRequestAttachmentController;
use App\Http\Controllers\ExternalPurchases\ShippingCompanyController;
use App\Http\Controllers\ExternalPurchases\ShipmentController;
use App\Http\Controllers\ExternalPurchases\VendorEmailTemplateController;
use App\Http\Controllers\Settings\ApprovalRuleController;
use App\Http\Controllers\Settings\PurchaseRequestApproverController;
use App\Http\Controllers\Settings\PurchaseRequestReminderRecipientController;
use App\Http\Controllers\Settings\MaintenanceReportMaterialApproverController;
use App\Http\Controllers\Settings\SalesRepresentativeController;
use App\Http\Controllers\Settings\QuoteDiscountLimitController;
use App\Http\Controllers\SplitUnits\SplitUnitController;
use App\Http\Controllers\Clients\ClientExtraFieldController;
use App\Http\Controllers\Agreements\AgreementController;

/*
|--------------------------------------------------------------------------
| Authentication Routes  (guests only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.authenticate');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('auth.logout')
    ->middleware('auth');

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| ERP Application Routes  (authenticated users)
|--------------------------------------------------------------------------
| Every route below (other than the dashboard and the generic /approvals
| inbox, which is authorized per-item inside its own controller — see
| ApprovalController — not by a module permission) is gated by a
| spatie/laravel-permission string via the 'permission:' middleware.
| See [[feedback_permissions_per_section]] in memory: every new
| section/action must add its permission string to PermissionSeeder AND
| wire it here + on its config/modules.php nav item, in the same task.
*/
Route::middleware(['auth', 'approval.gate'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Authorized per-row inside the controller (is this approval assigned to me?),
    // not by a module permission — deliberately ungated here.
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/',                [ApprovalController::class, 'index'])->name('index');
        Route::post('{approval}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('{approval}/reject',  [ApprovalController::class, 'reject'])->name('reject');
    });

    Route::prefix('settings')->name('settings.')->group(function () {

        // NOTE: the 'create' subset is always registered before 'index'+'show' for any resource
        // that has both — otherwise the show route's `{id}` wildcard (same segment count as the
        // literal "create" segment) would swallow "/create" first, since Laravel matches routes
        // in registration order. Keep this ordering for any future resource split the same way.
        Route::resource('users', UserController::class)->only(['create', 'store'])->middleware('permission:settings.users.create');
        Route::resource('users', UserController::class)->only(['index', 'show'])->middleware('permission:settings.users.view');
        Route::resource('users', UserController::class)->only(['edit', 'update'])->middleware('permission:settings.users.edit');
        Route::resource('users', UserController::class)->only(['destroy'])->middleware('permission:settings.users.delete');

        Route::resource('roles', RoleController::class)->only(['create', 'store'])->middleware('permission:settings.roles.create');
        Route::resource('roles', RoleController::class)->only(['index', 'show'])->middleware('permission:settings.roles.view');
        Route::resource('roles', RoleController::class)->only(['edit', 'update'])->middleware('permission:settings.roles.edit');
        Route::resource('roles', RoleController::class)->only(['destroy'])->middleware('permission:settings.roles.delete');

        Route::resource('permissions', PermissionController::class)->only(['index'])->middleware('permission:settings.permissions.view');

        Route::resource('branches', BranchController::class)->only(['index'])->middleware('permission:settings.branches.view');
        Route::resource('branches', BranchController::class)->only(['create', 'store'])->middleware('permission:settings.branches.create');
        Route::resource('branches', BranchController::class)->only(['edit', 'update'])->middleware('permission:settings.branches.edit');
        Route::resource('branches', BranchController::class)->only(['destroy'])->middleware('permission:settings.branches.delete');

        Route::resource('currencies', CurrencyController::class)->only(['index'])->middleware('permission:settings.currencies.view');
        Route::resource('currencies', CurrencyController::class)->only(['create', 'store'])->middleware('permission:settings.currencies.create');
        Route::resource('currencies', CurrencyController::class)->only(['edit', 'update'])->middleware('permission:settings.currencies.edit');
        Route::resource('currencies', CurrencyController::class)->only(['destroy'])->middleware('permission:settings.currencies.delete');

        Route::resource('countries', CountryController::class)->only(['index'])->middleware('permission:settings.countries.view');
        Route::resource('countries', CountryController::class)->only(['create', 'store'])->middleware('permission:settings.countries.create');
        Route::resource('countries', CountryController::class)->only(['edit', 'update'])->middleware('permission:settings.countries.edit');
        Route::resource('countries', CountryController::class)->only(['destroy'])->middleware('permission:settings.countries.delete');

        Route::resource('governorates', GovernorateController::class)->only(['index'])->middleware('permission:settings.governorates.view');
        Route::resource('governorates', GovernorateController::class)->only(['create', 'store'])->middleware('permission:settings.governorates.create');
        Route::resource('governorates', GovernorateController::class)->only(['edit', 'update'])->middleware('permission:settings.governorates.edit');
        Route::resource('governorates', GovernorateController::class)->only(['destroy'])->middleware('permission:settings.governorates.delete');

        Route::resource('activity-log', ActivityLogController::class)->only(['index'])->middleware('permission:settings.activity_log.view');

        Route::resource('approval-rules', ApprovalRuleController::class)->only(['index'])->middleware('permission:settings.approval_rules.view');
        Route::resource('approval-rules', ApprovalRuleController::class)->only(['store'])->middleware('permission:settings.approval_rules.edit');

        Route::middleware('permission:settings.purchase_request_approvers.edit')->group(function () {
            Route::get('purchase-request-approvers', [PurchaseRequestApproverController::class, 'index'])->name('purchase-request-approvers.index');
            Route::put('purchase-request-approvers', [PurchaseRequestApproverController::class, 'update'])->name('purchase-request-approvers.update');
        });

        Route::middleware('permission:settings.purchase_request_reminder_recipients.edit')->group(function () {
            Route::get('purchase-request-reminder-recipients', [PurchaseRequestReminderRecipientController::class, 'index'])->name('purchase-request-reminder-recipients.index');
            Route::put('purchase-request-reminder-recipients', [PurchaseRequestReminderRecipientController::class, 'update'])->name('purchase-request-reminder-recipients.update');
        });

        Route::middleware('permission:settings.maintenance_report_material_approvers.edit')->group(function () {
            Route::get('maintenance-report-material-approvers', [MaintenanceReportMaterialApproverController::class, 'index'])->name('maintenance-report-material-approvers.index');
            Route::put('maintenance-report-material-approvers', [MaintenanceReportMaterialApproverController::class, 'update'])->name('maintenance-report-material-approvers.update');
        });

        Route::resource('sales-representatives', SalesRepresentativeController::class)->only(['create', 'store'])->middleware('permission:settings.sales_representatives.create');
        Route::resource('sales-representatives', SalesRepresentativeController::class)->only(['index'])->middleware('permission:settings.sales_representatives.view');
        Route::resource('sales-representatives', SalesRepresentativeController::class)->only(['edit', 'update'])->middleware('permission:settings.sales_representatives.edit');
        Route::resource('sales-representatives', SalesRepresentativeController::class)->only(['destroy'])->middleware('permission:settings.sales_representatives.delete');

        Route::middleware('permission:settings.quote_discount_limit.edit')->group(function () {
            Route::get('quote-discount-limit', [QuoteDiscountLimitController::class, 'index'])->name('quote-discount-limit.index');
            Route::put('quote-discount-limit', [QuoteDiscountLimitController::class, 'update'])->name('quote-discount-limit.update');
        });

    });

    Route::prefix('warehouse')->name('warehouse.')->group(function () {

        Route::resource('warehouses', WarehouseController::class)->only(['index'])->middleware('permission:warehouse.view');
        Route::resource('warehouses', WarehouseController::class)->only(['create', 'store'])->middleware('permission:warehouse.warehouses.create');
        Route::resource('warehouses', WarehouseController::class)->only(['edit', 'update'])->middleware('permission:warehouse.warehouses.edit');
        Route::resource('warehouses', WarehouseController::class)->only(['destroy'])->middleware('permission:warehouse.warehouses.delete');

        Route::resource('categories', MaterialCategoryController::class)->only(['index'])->middleware('permission:warehouse.view');
        Route::resource('categories', MaterialCategoryController::class)->only(['create', 'store'])->middleware('permission:warehouse.categories.create');
        Route::resource('categories', MaterialCategoryController::class)->only(['edit', 'update'])->middleware('permission:warehouse.categories.edit');
        Route::resource('categories', MaterialCategoryController::class)->only(['destroy'])->middleware('permission:warehouse.categories.delete');

        Route::resource('units', UnitController::class)->only(['index'])->middleware('permission:warehouse.view');
        Route::resource('units', UnitController::class)->only(['create', 'store'])->middleware('permission:warehouse.units.create');
        Route::resource('units', UnitController::class)->only(['edit', 'update'])->middleware('permission:warehouse.units.edit');
        Route::resource('units', UnitController::class)->only(['destroy'])->middleware('permission:warehouse.units.delete');

        // Static "materials/..." segments must all be registered before the resource's
        // show route ({material} wildcard, same segment count as any of these).
        Route::get('materials/drafts', [MaterialController::class, 'drafts'])->name('materials.drafts')->middleware('permission:warehouse.view');
        Route::post('materials/bulk-destroy', [MaterialController::class, 'bulkDestroy'])->name('materials.bulk-destroy')->middleware('permission:warehouse.materials.delete');
        // Gated by quotations.view|tenders.view|split_units.view — only ever called from
        // Price Quote / CIAT Discount / Split Unit screens, already behind their own create permissions.
        Route::post('materials/quick-store', [MaterialController::class, 'quickStore'])->name('materials.quick-store')->middleware('permission:tenders.view|quotations.view|split_units.view');
        Route::resource('materials', MaterialController::class)->only(['create', 'store'])->middleware('permission:warehouse.materials.create');
        Route::resource('materials', MaterialController::class)->only(['index', 'show'])->middleware('permission:warehouse.view');
        Route::resource('materials', MaterialController::class)->only(['edit', 'update'])->middleware('permission:warehouse.materials.edit');
        Route::resource('materials', MaterialController::class)->only(['destroy'])->middleware('permission:warehouse.materials.delete');

        Route::resource('material-requests', MaterialRequestController::class)->only(['create', 'store'])->middleware('permission:warehouse.material_requests.create');
        Route::resource('material-requests', MaterialRequestController::class)->only(['index', 'show'])->middleware('permission:warehouse.view');
        Route::post('material-requests/{materialRequest}/request-approval', [MaterialRequestController::class, 'requestApproval'])
            ->name('material-requests.request-approval')->middleware('permission:warehouse.material_requests.create');
        Route::post('material-requests/{materialRequest}/fulfill', [MaterialRequestController::class, 'fulfill'])
            ->name('material-requests.fulfill')->middleware('permission:warehouse.material_requests.fulfill');

        Route::prefix('vouchers/{type}')->where(['type' => 'receipt|issue|transfer'])->name('vouchers.')->group(function () {
            Route::get('/',        [StockVoucherController::class, 'index'])->name('index')->middleware('permission:warehouse.view');
            Route::get('create',   [StockVoucherController::class, 'create'])->name('create')->middleware('permission:warehouse.vouchers.create');
            Route::post('/',       [StockVoucherController::class, 'store'])->name('store')->middleware('permission:warehouse.vouchers.create');
            Route::get('{voucher}', [StockVoucherController::class, 'show'])->name('show')->middleware('permission:warehouse.view');
            Route::post('{voucher}/post', [StockVoucherController::class, 'post'])->name('post')->middleware('permission:warehouse.vouchers.post');
        });

        Route::middleware('permission:warehouse.reports.view')->group(function () {
            Route::get('reports/materials',  [WarehouseReportController::class, 'materials'])->name('reports.materials');
            Route::get('reports/stocktake',  [WarehouseReportController::class, 'stocktake'])->name('reports.stocktake');
        });

    });

    // ── Accounting (invoices and invoice types only) ───────────────────────────
    Route::prefix('accounting')->name('accounting.')->group(function () {

        Route::resource('invoice-types', InvoiceTypeController::class)->only(['index'])->middleware('permission:accounting.view');
        Route::resource('invoice-types', InvoiceTypeController::class)->only(['create', 'store'])->middleware('permission:accounting.invoice_types.create');
        Route::resource('invoice-types', InvoiceTypeController::class)->only(['edit', 'update'])->middleware('permission:accounting.invoice_types.edit');
        Route::resource('invoice-types', InvoiceTypeController::class)->only(['destroy'])->middleware('permission:accounting.invoice_types.delete');

        Route::resource('invoices', InvoiceController::class)->only(['create', 'store'])->middleware('permission:accounting.invoices.create');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show'])->middleware('permission:accounting.view');
        Route::post('invoices/{invoice}/receipts', [InvoiceReceiptController::class, 'store'])->name('invoices.receipts.store')->middleware('permission:accounting.invoices.create');
        Route::delete('invoices/{invoice}/receipts/{receipt}', [InvoiceReceiptController::class, 'destroy'])->name('invoices.receipts.destroy')->middleware('permission:accounting.invoices.receipts.delete');

    });

    // ── Clients (customers, suppliers and groups — moved out of accounting prefix but
    //    route NAMES stay as accounting.customers.* so existing blade templates need no changes) ──
    Route::prefix('clients')->name('accounting.')->group(function () {

        // Customers and suppliers are separate tables/models/controllers — each
        // is its own domain, not a shared "party" concept.
        Route::resource('customers', CustomerController::class)->only(['index'])->middleware('permission:clients.view|accounting.view');
        Route::resource('customers', CustomerController::class)->only(['create', 'store'])->middleware('permission:clients.customers.create|accounting.customers.create');
        Route::resource('customers', CustomerController::class)->only(['edit', 'update'])->middleware('permission:clients.customers.edit|accounting.customers.edit');
        Route::resource('customers', CustomerController::class)->only(['destroy'])->middleware('permission:clients.customers.delete|accounting.customers.delete');

        Route::middleware('permission:clients.customers.create|accounting.customers.create')->group(function () {
            Route::get('customers-import-template', [CustomerController::class, 'importTemplate'])->name('customers.import-template');
            Route::post('customers-import', [CustomerController::class, 'import'])->name('customers.import');
        });

        Route::resource('customer-groups', CustomerGroupController::class)->only(['index'])->middleware('permission:clients.view|accounting.view');
        Route::resource('customer-groups', CustomerGroupController::class)->only(['create', 'store'])->middleware('permission:clients.customer_groups.create|accounting.customer_groups.create');
        Route::resource('customer-groups', CustomerGroupController::class)->only(['edit', 'update'])->middleware('permission:clients.customer_groups.edit|accounting.customer_groups.edit');
        Route::resource('customer-groups', CustomerGroupController::class)->only(['destroy'])->middleware('permission:clients.customer_groups.delete|accounting.customer_groups.delete');

        Route::post('suppliers/shipping-instruction-image', [SupplierController::class, 'uploadShippingInstructionImage'])
            ->name('suppliers.shipping-instruction-image')->middleware('permission:clients.suppliers.edit|accounting.suppliers.edit');
        Route::resource('suppliers', SupplierController::class)->only(['index'])->middleware('permission:clients.view|accounting.view');
        Route::resource('suppliers', SupplierController::class)->only(['create', 'store'])->middleware('permission:clients.suppliers.create|accounting.suppliers.create');
        Route::resource('suppliers', SupplierController::class)->only(['edit', 'update'])->middleware('permission:clients.suppliers.edit|accounting.suppliers.edit');
        Route::resource('suppliers', SupplierController::class)->only(['destroy'])->middleware('permission:clients.suppliers.delete|accounting.suppliers.delete');

        Route::middleware('permission:clients.suppliers.create|accounting.suppliers.create')->group(function () {
            Route::get('suppliers-import-template', [SupplierController::class, 'importTemplate'])->name('suppliers.import-template');
            Route::post('suppliers-import', [SupplierController::class, 'import'])->name('suppliers.import');
        });

        Route::resource('supplier-groups', SupplierGroupController::class)->only(['index'])->middleware('permission:clients.view|accounting.view');
        Route::resource('supplier-groups', SupplierGroupController::class)->only(['create', 'store'])->middleware('permission:clients.supplier_groups.create|accounting.supplier_groups.create');
        Route::resource('supplier-groups', SupplierGroupController::class)->only(['edit', 'update'])->middleware('permission:clients.supplier_groups.edit|accounting.supplier_groups.edit');
        Route::resource('supplier-groups', SupplierGroupController::class)->only(['destroy'])->middleware('permission:clients.supplier_groups.delete|accounting.supplier_groups.delete');

    });

    // ── Client Extra Fields ────────────────────────────────────────────────────
    Route::prefix('client-extra-fields')->name('client-extra-fields.')->group(function () {
        Route::get('customers', [ClientExtraFieldController::class, 'customers'])->name('customers')->middleware('permission:clients.extra_fields.view');
        Route::get('suppliers', [ClientExtraFieldController::class, 'suppliers'])->name('suppliers')->middleware('permission:clients.extra_fields.view');
        Route::get('tenders', [ClientExtraFieldController::class, 'tenders'])->name('tenders')->middleware('permission:clients.extra_fields.view');
        Route::post('/', [ClientExtraFieldController::class, 'store'])->name('store')->middleware('permission:clients.extra_fields.create');
        Route::get('{clientExtraField}/edit', [ClientExtraFieldController::class, 'edit'])->name('edit')->middleware('permission:clients.extra_fields.edit');
        Route::put('{clientExtraField}', [ClientExtraFieldController::class, 'update'])->name('update')->middleware('permission:clients.extra_fields.edit');
        Route::delete('{clientExtraField}', [ClientExtraFieldController::class, 'destroy'])->name('destroy')->middleware('permission:clients.extra_fields.delete');
    });

    Route::resource('tenders', TenderController::class)->only(['create', 'store'])->middleware('permission:tenders.create');
    Route::resource('tenders', TenderController::class)->only(['index', 'show'])->middleware('permission:tenders.view');
    Route::resource('tenders', TenderController::class)->only(['edit', 'update'])->middleware('permission:tenders.edit');
    Route::resource('tenders', TenderController::class)->only(['destroy'])->middleware('permission:tenders.delete');
    Route::post('tenders/{tender}/attach-quote', [TenderController::class, 'attachPriceQuote'])->name('tenders.attach-quote')->middleware('permission:tenders.edit');

    Route::resource('tender-statuses', TenderStatusController::class)->only(['index'])->middleware('permission:tenders.view');
    Route::resource('tender-statuses', TenderStatusController::class)->only(['create', 'store'])->middleware('permission:tenders.tender_statuses.create');
    Route::resource('tender-statuses', TenderStatusController::class)->only(['edit', 'update'])->middleware('permission:tenders.tender_statuses.edit');
    Route::resource('tender-statuses', TenderStatusController::class)->only(['destroy'])->middleware('permission:tenders.tender_statuses.delete');

    Route::resource('quote-supply-scopes', QuoteSupplyScopeController::class)->only(['index'])->middleware('permission:tenders.view');
    Route::resource('quote-supply-scopes', QuoteSupplyScopeController::class)->only(['create', 'store'])->middleware('permission:tenders.quote_supply_scopes.create');
    Route::resource('quote-supply-scopes', QuoteSupplyScopeController::class)->only(['edit', 'update'])->middleware('permission:tenders.quote_supply_scopes.edit');
    Route::resource('quote-supply-scopes', QuoteSupplyScopeController::class)->only(['destroy'])->middleware('permission:tenders.quote_supply_scopes.delete');

    Route::resource('quote-delivery-terms', QuoteDeliveryTermController::class)->only(['index'])->middleware('permission:tenders.view');
    Route::resource('quote-delivery-terms', QuoteDeliveryTermController::class)->only(['create', 'store'])->middleware('permission:tenders.quote_delivery_terms.create');
    Route::resource('quote-delivery-terms', QuoteDeliveryTermController::class)->only(['edit', 'update'])->middleware('permission:tenders.quote_delivery_terms.edit');
    Route::resource('quote-delivery-terms', QuoteDeliveryTermController::class)->only(['destroy'])->middleware('permission:tenders.quote_delivery_terms.delete');

    Route::resource('ciat-discounts', CiatDiscountController::class)->only(['index'])->middleware('permission:analyses.view|tenders.view');
    Route::resource('ciat-discounts', CiatDiscountController::class)->only(['create', 'store'])->middleware('permission:analyses.ciat_discounts.create|tenders.ciat_discounts.create');
    Route::resource('ciat-discounts', CiatDiscountController::class)->only(['edit', 'update'])->middleware('permission:analyses.ciat_discounts.edit|tenders.ciat_discounts.edit');
    Route::resource('ciat-discounts', CiatDiscountController::class)->only(['destroy'])->middleware('permission:analyses.ciat_discounts.delete|tenders.ciat_discounts.delete');

    Route::resource('price-analyses', PriceAnalysisController::class)->only(['create', 'store'])->middleware('permission:analyses.price_analyses.create|tenders.price_analyses.create');
    Route::resource('price-analyses', PriceAnalysisController::class)->only(['index', 'show'])->middleware('permission:analyses.view|tenders.view');
    Route::resource('price-analyses', PriceAnalysisController::class)->only(['edit', 'update'])->middleware('permission:analyses.price_analyses.edit|tenders.price_analyses.edit');
    Route::resource('price-analyses', PriceAnalysisController::class)->only(['destroy'])->middleware('permission:analyses.price_analyses.delete|tenders.price_analyses.delete');
    Route::patch('price-analysis-items/{item}/x8', [PriceAnalysisController::class, 'updateX8'])->name('price-analysis-items.x8.update')->middleware('permission:analyses.price_analyses.edit|tenders.price_analyses.edit');

    // ── Price Quotes (now standalone quotations module — still accessible from tenders/maintenance flows) ──
    Route::get('price-quotes/{priceQuote}/print', [PriceQuoteController::class, 'printDocument'])->name('price-quotes.print')->middleware('permission:quotations.view|tenders.view|maintenance.view');
    Route::post('price-quotes/assign', [PriceQuoteController::class, 'assign'])->name('price-quotes.assign')->middleware('permission:quotations.price_quotes.assign|tenders.price_quotes.assign');
    Route::post('price-quotes/{priceQuote}/convert-to-invoice', [PriceQuoteController::class, 'convertToInvoice'])->name('price-quotes.convert-to-invoice')->middleware('permission:quotations.price_quotes.convert_to_invoice|tenders.price_quotes.convert_to_invoice');
    Route::post('price-quotes/{priceQuote}/convert-to-project', [TenderController::class, 'convertToProject'])->name('price-quotes.convert-to-project')->middleware('permission:tenders.projects.convert');
    Route::resource('price-quotes', PriceQuoteController::class)->only(['create', 'store'])->middleware('permission:quotations.price_quotes.create|tenders.price_quotes.create');
    Route::resource('price-quotes', PriceQuoteController::class)->only(['index', 'show'])->middleware('permission:quotations.view|tenders.view|maintenance.view');
    Route::resource('price-quotes', PriceQuoteController::class)->only(['edit', 'update'])->middleware('permission:quotations.price_quotes.edit|tenders.price_quotes.edit');

    // ── Projects (now standalone module — still created via tenders convert-to-project) ──
    Route::resource('projects', ProjectController::class)->only(['index', 'show'])->middleware('permission:projects.view|tenders.projects.view');
    Route::resource('projects', ProjectController::class)->only(['edit', 'update'])->middleware('permission:projects.edit|tenders.projects.edit');
    Route::resource('projects', ProjectController::class)->only(['destroy'])->middleware('permission:projects.delete|tenders.projects.delete');
    Route::post('projects/{project}/attachments', [ProjectAttachmentController::class, 'store'])->name('projects.attachments.store')->middleware('permission:projects.edit|tenders.projects.edit');
    Route::delete('projects/{project}/attachments/{attachment}', [ProjectAttachmentController::class, 'destroy'])->name('projects.attachments.destroy')->middleware('permission:projects.edit|tenders.projects.edit');
    Route::post('projects/{project}/item-assignments', [ProjectItemAssignmentController::class, 'store'])->name('projects.item-assignments.store')->middleware('permission:projects.edit|tenders.projects.edit');
    // No extra permission string — the row's own `assigned_to`/`manager_id` ownership check inside
    // the controller is the real gate here, same precedent as ApprovalController's approve/reject.
    Route::post('projects/{project}/item-assignments/{assignment}/mark-done', [ProjectItemAssignmentController::class, 'markDone'])->name('projects.item-assignments.mark-done');
    // No extra permission string — same ownership-check-as-gate precedent as mark-done above.
    Route::post('projects/{project}/item-assignments/{assignment}/comments', [ProjectItemAssignmentController::class, 'storeComment'])->name('projects.item-assignments.comments.store');
    Route::post('projects/{project}/assign-team', [ProjectController::class, 'assignTeam'])->name('projects.assign-team');
    Route::post('projects/{project}/assign-manager', [ProjectController::class, 'assignManager'])->name('projects.assign-manager')->middleware('permission:projects.edit|tenders.projects.edit');
    Route::post('projects/{project}/questions', [ProjectQuestionController::class, 'store'])->name('projects.questions.store')->middleware('permission:projects.edit|tenders.projects.edit');
    Route::post('projects/{project}/questions/{question}/answer', [ProjectQuestionController::class, 'answer'])->name('projects.questions.answer');
    Route::post('projects/{project}/devices', [ProjectDeviceController::class, 'store'])->name('projects.devices.store')->middleware('permission:projects.edit|tenders.projects.edit');
    Route::delete('projects/{project}/devices/{device}', [ProjectDeviceController::class, 'destroy'])->name('projects.devices.destroy')->middleware('permission:projects.edit|tenders.projects.edit');

    // ── Projects Under Review (new standalone module — tenders land here before approval) ─────
    Route::get('project-reviews', [ProjectReviewController::class, 'index'])->name('project-reviews.index')->middleware('permission:projects_under_review.view');
    Route::post('project-reviews/{project}/approve', [ProjectReviewController::class, 'approve'])->name('project-reviews.approve')->middleware('permission:projects_under_review.approve');
    Route::post('project-reviews/{project}/reject', [ProjectReviewController::class, 'reject'])->name('project-reviews.reject')->middleware('permission:projects_under_review.reject');
    Route::post('project-reviews/variation-orders/{variationOrder}/approve', [ProjectReviewController::class, 'approveVariationOrder'])->name('project-reviews.variation-orders.approve')->middleware('permission:projects_under_review.approve');
    Route::post('project-reviews/variation-orders/{variationOrder}/reject', [ProjectReviewController::class, 'rejectVariationOrder'])->name('project-reviews.variation-orders.reject')->middleware('permission:projects_under_review.reject');

    Route::resource('purchase-request-reminders', PurchaseRequestReminderController::class)->only(['create', 'store'])->middleware('permission:tenders.purchase_request_reminders.create');
    Route::resource('purchase-request-reminders', PurchaseRequestReminderController::class)->only(['index', 'show'])->middleware('permission:tenders.view');
    Route::resource('purchase-request-reminders', PurchaseRequestReminderController::class)->only(['edit', 'update'])->middleware('permission:tenders.purchase_request_reminders.edit');
    Route::resource('purchase-request-reminders', PurchaseRequestReminderController::class)->only(['destroy'])->middleware('permission:tenders.purchase_request_reminders.delete');

    // ── Split Units (new standalone module) ───────────────────────────────────
    // create/store MUST be registered before index/show — see the Agreements block's comment
    // above for why (show's {splitUnit} wildcard would otherwise swallow the literal "create").
    Route::resource('split-units', SplitUnitController::class)->only(['create', 'store'])->middleware('permission:split_units.create');
    Route::get('split-units/{splitUnit}/print', [SplitUnitController::class, 'printDocument'])->name('split-units.print')->middleware('permission:split_units.view');
    Route::resource('split-units', SplitUnitController::class)->only(['index', 'show'])->middleware('permission:split_units.view');
    Route::resource('split-units', SplitUnitController::class)->only(['edit', 'update'])->middleware('permission:split_units.edit');
    Route::resource('split-units', SplitUnitController::class)->only(['destroy'])->middleware('permission:split_units.delete');

    // ── Agreements (new standalone module) ─────────────────────────────────────
    // create/store MUST be registered before index/show — show's {agreement} wildcard would
    // otherwise match "create" as a literal id first (registration order wins) and 404 via
    // ModelNotFoundException. See maintenance-reports/price-quotes above for the same ordering.
    Route::resource('agreements', AgreementController::class)->only(['create', 'store'])->middleware('permission:agreements.create');
    Route::resource('agreements', AgreementController::class)->only(['index', 'show'])->middleware('permission:agreements.view');
    Route::resource('agreements', AgreementController::class)->only(['edit', 'update'])->middleware('permission:agreements.edit');
    Route::resource('agreements', AgreementController::class)->only(['destroy'])->middleware('permission:agreements.delete');

    // Must be registered before the {purchase_request} resource routes below, since
    // "ship" would otherwise be swallowed by the show route's single-segment wildcard.
    Route::get('purchase-requests/ship', [PurchaseRequestController::class, 'shipmentForm'])->name('purchase-requests.ship')->middleware('permission:external_purchases.purchase_requests.ship');
    Route::post('purchase-requests/ship', [PurchaseRequestController::class, 'sendToShippingCompanies'])->name('purchase-requests.ship.send')->middleware('permission:external_purchases.purchase_requests.ship');

    Route::resource('purchase-requests', PurchaseRequestController::class)->only(['create', 'store'])->middleware('permission:external_purchases.purchase_requests.create');
    Route::resource('purchase-requests', PurchaseRequestController::class)->only(['index', 'show'])->middleware('permission:external_purchases.view');
    Route::resource('purchase-requests', PurchaseRequestController::class)->only(['edit', 'update'])->middleware('permission:external_purchases.purchase_requests.edit');
    Route::resource('purchase-requests', PurchaseRequestController::class)->only(['destroy'])->middleware('permission:external_purchases.purchase_requests.delete');
    Route::get('purchase-requests/{purchaseRequest}/print', [PurchaseRequestController::class, 'printDocument'])->name('purchase-requests.print')->middleware('permission:external_purchases.view');
    Route::post('purchase-requests/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve')->middleware('permission:external_purchases.purchase_requests.approve');
    Route::post('purchase-requests/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])->name('purchase-requests.reject')->middleware('permission:external_purchases.purchase_requests.approve');
    Route::post('purchase-requests/{purchaseRequest}/approve-manually', [PurchaseRequestController::class, 'approveManually'])->name('purchase-requests.approve-manually')->middleware('permission:external_purchases.purchase_requests.approve');
    Route::post('purchase-requests/{purchaseRequest}/mark-sent', [PurchaseRequestController::class, 'markSent'])->name('purchase-requests.mark-sent')->middleware('permission:external_purchases.purchase_requests.mark_sent');
    Route::post('purchase-requests/{purchaseRequest}/manufacturing', [PurchaseRequestController::class, 'updateManufacturing'])->name('purchase-requests.manufacturing')->middleware('permission:external_purchases.purchase_requests.edit');
    Route::post('purchase-requests/{purchaseRequest}/ready-for-shipping', [PurchaseRequestController::class, 'readyForShipping'])->name('purchase-requests.ready-for-shipping')->middleware('permission:external_purchases.purchase_requests.edit');
    Route::post('purchase-requests/{purchaseRequest}/attachments', [PurchaseRequestAttachmentController::class, 'store'])->name('purchase-requests.attachments.store')->middleware('permission:external_purchases.purchase_requests.edit');
    Route::delete('purchase-requests/{purchaseRequest}/attachments/{attachment}', [PurchaseRequestAttachmentController::class, 'destroy'])->name('purchase-requests.attachments.destroy')->middleware('permission:external_purchases.purchase_requests.edit');

    Route::resource('shipping-companies', ShippingCompanyController::class)->only(['index'])->middleware('permission:external_purchases.view');
    Route::resource('shipping-companies', ShippingCompanyController::class)->only(['create', 'store'])->middleware('permission:external_purchases.shipping_companies.create');
    Route::resource('shipping-companies', ShippingCompanyController::class)->only(['edit', 'update'])->middleware('permission:external_purchases.shipping_companies.edit');
    Route::resource('shipping-companies', ShippingCompanyController::class)->only(['destroy'])->middleware('permission:external_purchases.shipping_companies.delete');

    Route::resource('shipments', ShipmentController::class)->only(['create', 'store'])->middleware('permission:external_purchases.shipments.create');
    Route::resource('shipments', ShipmentController::class)->only(['index', 'show'])->middleware('permission:external_purchases.view');
    Route::resource('shipments', ShipmentController::class)->only(['edit', 'update'])->middleware('permission:external_purchases.shipments.edit');
    Route::resource('shipments', ShipmentController::class)->only(['destroy'])->middleware('permission:external_purchases.shipments.delete');

    Route::resource('appointment-types', AppointmentTypeController::class)->only(['index'])->middleware('permission:crm.view');
    Route::resource('appointment-types', AppointmentTypeController::class)->only(['create', 'store'])->middleware('permission:crm.appointment_types.create');
    Route::resource('appointment-types', AppointmentTypeController::class)->only(['edit', 'update'])->middleware('permission:crm.appointment_types.edit');
    Route::resource('appointment-types', AppointmentTypeController::class)->only(['destroy'])->middleware('permission:crm.appointment_types.delete');

    Route::resource('report-templates', ReportTemplateController::class)->only(['index'])->middleware('permission:maintenance.view');
    Route::resource('report-templates', ReportTemplateController::class)->only(['create', 'store'])->middleware('permission:maintenance.report_templates.create');
    Route::resource('report-templates', ReportTemplateController::class)->only(['edit', 'update'])->middleware('permission:maintenance.report_templates.edit');
    Route::resource('report-templates', ReportTemplateController::class)->only(['destroy'])->middleware('permission:maintenance.report_templates.delete');

    Route::get('maintenance-reports/search', [MaintenanceReportController::class, 'search'])->name('maintenance-reports.search')->middleware('permission:maintenance.view');
    Route::resource('maintenance-reports', MaintenanceReportController::class)->only(['create', 'store'])->middleware('permission:maintenance.reports.create');
    Route::resource('maintenance-reports', MaintenanceReportController::class)->only(['index', 'show'])->middleware('permission:maintenance.view');
    Route::resource('maintenance-reports', MaintenanceReportController::class)->only(['edit', 'update'])->middleware('permission:maintenance.reports.edit');
    Route::resource('maintenance-reports', MaintenanceReportController::class)->only(['destroy'])->middleware('permission:maintenance.reports.delete');
    Route::post('maintenance-reports/{maintenanceReport}/approve-materials', [MaintenanceReportController::class, 'approveMaterials'])->name('maintenance-reports.approve-materials')->middleware('permission:maintenance.reports.approve_materials');
    Route::post('maintenance-reports/{maintenanceReport}/reject-materials', [MaintenanceReportController::class, 'rejectMaterials'])->name('maintenance-reports.reject-materials')->middleware('permission:maintenance.reports.approve_materials');
    Route::post('maintenance-reports/{maintenanceReport}/convert-to-quote', [MaintenanceReportController::class, 'convertToQuote'])->name('maintenance-reports.convert-to-quote')->middleware('permission:maintenance.reports.convert_to_quote');

    // Replaces the old m/maintenance-reports one-shot mobile flow — see MobileVisitController.
    Route::prefix('m/visits')->name('maintenance-visits.mobile.')->middleware('permission:maintenance.reports.create')->group(function () {
        Route::get('create', [MobileVisitController::class, 'create'])->name('create');
        Route::post('/', [MobileVisitController::class, 'store'])->name('store');
        Route::get('{maintenanceVisit}', [MobileVisitController::class, 'show'])->name('show');
        Route::post('{maintenanceVisit}/reports', [MobileVisitController::class, 'storeReport'])->name('reports.store');
        Route::patch('{maintenanceVisit}/reports/{maintenanceReport}', [MobileVisitController::class, 'updateReport'])->name('reports.update');
        Route::patch('{maintenanceVisit}/reports/{maintenanceReport}/answer', [MobileVisitController::class, 'updateAnswer'])->name('reports.answer');
        Route::post('{maintenanceVisit}/reports/{maintenanceReport}/materials', [MobileVisitController::class, 'syncMaterialsForReport'])->name('reports.materials');
        Route::post('{maintenanceVisit}/finish', [MobileVisitController::class, 'finish'])->name('finish');
    });

    Route::resource('maintenance-visit-types', MaintenanceVisitTypeController::class)->only(['index'])->middleware('permission:maintenance.view');
    Route::resource('maintenance-visit-types', MaintenanceVisitTypeController::class)->only(['create', 'store'])->middleware('permission:maintenance.visit_types.create');
    Route::resource('maintenance-visit-types', MaintenanceVisitTypeController::class)->only(['edit', 'update'])->middleware('permission:maintenance.visit_types.edit');
    Route::resource('maintenance-visit-types', MaintenanceVisitTypeController::class)->only(['destroy'])->middleware('permission:maintenance.visit_types.delete');

    Route::get('maintenance-requests', [MaintenanceRequestController::class, 'index'])->name('maintenance-requests.index')->middleware('permission:maintenance.requests.manage');
    Route::post('maintenance-requests/{maintenanceRequest}/approve', [MaintenanceRequestController::class, 'approve'])->name('maintenance-requests.approve')->middleware('permission:maintenance.requests.manage');
    Route::post('maintenance-requests/{maintenanceRequest}/reject', [MaintenanceRequestController::class, 'reject'])->name('maintenance-requests.reject')->middleware('permission:maintenance.requests.manage');

    Route::resource('maintenance-visits', MaintenanceVisitController::class)->only(['index', 'show'])->middleware('permission:maintenance.visits.manage');
    Route::post('maintenance-visits/{maintenanceVisit}/classify', [MaintenanceVisitController::class, 'classify'])->name('maintenance-visits.classify')->middleware('permission:maintenance.visits.manage');

    Route::resource('maintenance-device-passwords', DevicePasswordController::class)->only(['index'])->middleware('permission:maintenance.view');
    Route::resource('maintenance-device-passwords', DevicePasswordController::class)->only(['create', 'store'])->middleware('permission:maintenance.device_passwords.create');
    Route::resource('maintenance-device-passwords', DevicePasswordController::class)->only(['edit', 'update'])->middleware('permission:maintenance.device_passwords.edit');
    Route::resource('maintenance-device-passwords', DevicePasswordController::class)->only(['destroy'])->middleware('permission:maintenance.device_passwords.delete');

    Route::resource('maintenance-contracts', MaintenanceContractController::class)->only(['create', 'store'])->middleware('permission:maintenance.contracts.create');
    Route::resource('maintenance-contracts', MaintenanceContractController::class)->only(['index', 'show'])->middleware('permission:maintenance.view');
    Route::resource('maintenance-contracts', MaintenanceContractController::class)->only(['edit', 'update'])->middleware('permission:maintenance.contracts.edit');
    Route::resource('maintenance-contracts', MaintenanceContractController::class)->only(['destroy'])->middleware('permission:maintenance.contracts.delete');
    Route::post('maintenance-contracts/{maintenanceContract}/payments', [ContractPaymentController::class, 'store'])->name('contract-payments.store')->middleware('permission:maintenance.contract_payments.create');
    Route::delete('maintenance-contracts/{maintenanceContract}/payments/{payment}', [ContractPaymentController::class, 'destroy'])->name('contract-payments.destroy')->middleware('permission:maintenance.contract_payments.delete');
    Route::get('contract-payments', [ContractPaymentController::class, 'index'])->name('contract-payments.index')->middleware('permission:maintenance.view');
    Route::post('contract-payments/assign', [ContractPaymentController::class, 'assign'])->name('contract-payments.assign')->middleware('permission:maintenance.contract_payments.assign');
    Route::post('contract-payments/{payment}/convert-to-invoice', [ContractPaymentController::class, 'convertToInvoice'])->name('contract-payments.convert-to-invoice')->middleware('permission:maintenance.contract_payments.convert_to_invoice');

    Route::post('maintenance-contracts/{maintenanceContract}/scheduled-visits', [ContractScheduledVisitController::class, 'store'])->name('contract-scheduled-visits.store')->middleware('permission:maintenance.contract_scheduled_visits.create');
    Route::delete('maintenance-contracts/{maintenanceContract}/scheduled-visits/{scheduledVisit}', [ContractScheduledVisitController::class, 'destroy'])->name('contract-scheduled-visits.destroy')->middleware('permission:maintenance.contract_scheduled_visits.delete');
    Route::get('contract-scheduled-visits', [ContractScheduledVisitController::class, 'index'])->name('contract-scheduled-visits.index')->middleware('permission:maintenance.view');

    Route::patch('appointments/{appointment}/toggle-complete', [AppointmentController::class, 'toggleComplete'])->name('appointments.toggle-complete')->middleware('permission:crm.appointments.edit');
    Route::resource('appointments', AppointmentController::class)->only(['index'])->middleware('permission:crm.view');
    Route::resource('appointments', AppointmentController::class)->only(['create', 'store'])->middleware('permission:crm.appointments.create');
    Route::resource('appointments', AppointmentController::class)->only(['edit', 'update'])->middleware('permission:crm.appointments.edit');
    Route::resource('appointments', AppointmentController::class)->only(['destroy'])->middleware('permission:crm.appointments.delete');

    Route::middleware('permission:external_purchases.vendor_email_template.edit')->group(function () {
        Route::get('vendor-email-template', [VendorEmailTemplateController::class, 'index'])->name('vendor-email-template.index');
        Route::put('vendor-email-template', [VendorEmailTemplateController::class, 'update'])->name('vendor-email-template.update');
    });

});

/*
|--------------------------------------------------------------------------
| Customer Portal Routes  (separate 'customer' guard — never caught by the
| 'auth'/'approval.gate' group above, which is the staff 'user' guard's own).
|--------------------------------------------------------------------------
*/
Route::prefix('customer-portal')->name('customer-portal.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('login',  [CustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [CustomerAuthController::class, 'login'])->name('authenticate');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');
        Route::get('/', [CustomerPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('contracts/{maintenanceContract}', [CustomerPortalController::class, 'showContract'])->name('contracts.show');
        Route::get('maintenance-requests/create', [CustomerPortalController::class, 'createRequest'])->name('maintenance-requests.create');
        Route::post('maintenance-requests', [CustomerPortalController::class, 'storeRequest'])->name('maintenance-requests.store');
        Route::get('visits/{maintenanceVisit}', [CustomerPortalController::class, 'showVisit'])->name('visits.show');
        Route::post('visits/{maintenanceVisit}/sign', [CustomerPortalController::class, 'signVisit'])->name('visits.sign');
    });
});
