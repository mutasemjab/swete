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
use App\Http\Controllers\Tenders\TenderController;
use App\Http\Controllers\Tenders\TenderStatusController;
use App\Http\Controllers\Tenders\PriceQuoteController;
use App\Http\Controllers\Tenders\ProjectController;
use App\Http\Controllers\ExternalPurchases\PurchaseRequestController;
use App\Http\Controllers\Settings\ApprovalRuleController;

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
*/
Route::middleware(['auth', 'approval.gate'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/',                [ApprovalController::class, 'index'])->name('index');
        Route::post('{approval}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('{approval}/reject',  [ApprovalController::class, 'reject'])->name('reject');
    });

    Route::prefix('settings')->name('settings.')->group(function () {

        Route::resource('users',       UserController::class);
        Route::resource('roles',       RoleController::class);
        Route::resource('permissions', PermissionController::class)->only(['index']);
        Route::resource('branches',    BranchController::class)->except(['show']);
        Route::resource('currencies',  CurrencyController::class)->except(['show']);
        Route::resource('countries',   CountryController::class)->except(['show']);
        Route::resource('activity-log', ActivityLogController::class)->only(['index']);
        Route::resource('approval-rules', ApprovalRuleController::class)->only(['index', 'store']);

    });

    Route::prefix('warehouse')->name('warehouse.')->group(function () {

        Route::resource('warehouses', WarehouseController::class)->except(['show']);
        Route::resource('categories', MaterialCategoryController::class)->except(['show']);
        Route::resource('units',      UnitController::class)->except(['show']);
        Route::resource('materials',  MaterialController::class);

        Route::resource('material-requests', MaterialRequestController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('material-requests/{materialRequest}/request-approval', [MaterialRequestController::class, 'requestApproval'])
            ->name('material-requests.request-approval');
        Route::post('material-requests/{materialRequest}/fulfill', [MaterialRequestController::class, 'fulfill'])
            ->name('material-requests.fulfill');

        Route::prefix('vouchers/{type}')->where(['type' => 'receipt|issue|transfer'])->name('vouchers.')->group(function () {
            Route::get('/',        [StockVoucherController::class, 'index'])->name('index');
            Route::get('create',   [StockVoucherController::class, 'create'])->name('create');
            Route::post('/',       [StockVoucherController::class, 'store'])->name('store');
            Route::get('{voucher}', [StockVoucherController::class, 'show'])->name('show');
            Route::post('{voucher}/post', [StockVoucherController::class, 'post'])->name('post');
        });

        Route::get('reports/materials',  [WarehouseReportController::class, 'materials'])->name('reports.materials');
        Route::get('reports/stocktake',  [WarehouseReportController::class, 'stocktake'])->name('reports.stocktake');

    });

    Route::prefix('accounting')->name('accounting.')->group(function () {

        // Customers and suppliers are separate tables/models/controllers — each
        // is its own domain, not a shared "party" concept.
        Route::resource('customers',        CustomerController::class)->except(['show']);
        Route::resource('customer-groups',  CustomerGroupController::class)->except(['show']);
        Route::resource('suppliers',        SupplierController::class)->except(['show']);
        Route::resource('supplier-groups',  SupplierGroupController::class)->except(['show']);

        Route::resource('invoice-types', InvoiceTypeController::class)->except(['show']);
        Route::resource('invoices',      InvoiceController::class)->only(['index', 'create', 'store', 'show']);

    });

    Route::resource('tenders', TenderController::class);
    Route::post('tenders/{tender}/attach-quote', [TenderController::class, 'attachPriceQuote'])->name('tenders.attach-quote');
    Route::post('tenders/{tender}/convert-to-project', [TenderController::class, 'convertToProject'])->name('tenders.convert-to-project');

    Route::resource('tender-statuses', TenderStatusController::class)->except(['show']);
    Route::resource('price-quotes', PriceQuoteController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('projects', ProjectController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    Route::resource('purchase-requests', PurchaseRequestController::class)->only(['index', 'create', 'store', 'show']);

});
