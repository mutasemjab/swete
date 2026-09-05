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
use App\Http\Controllers\Settings\ActivityLogController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Warehouse\MaterialCategoryController;
use App\Http\Controllers\Warehouse\UnitController;
use App\Http\Controllers\Warehouse\MaterialController;
use App\Http\Controllers\Warehouse\StockVoucherController;
use App\Http\Controllers\Warehouse\MaterialRequestController;
use App\Http\Controllers\Warehouse\WarehouseReportController;
use App\Http\Controllers\Accounting\PartyGroupController;
use App\Http\Controllers\Accounting\PartyController;
use App\Http\Controllers\Accounting\InvoiceTypeController;
use App\Http\Controllers\Accounting\InvoiceController;
use App\Http\Controllers\Tenders\TenderController;
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

        // Customers & suppliers share one controller pair, differing only by the
        // `type` route default — see Warehouse\StockVoucherController for the same pattern.
        foreach (['customer' => 'customers', 'supplier' => 'suppliers'] as $type => $uri) {

            Route::prefix($uri)->name("{$uri}.")->group(function () use ($type) {
                Route::get('/',            [PartyController::class, 'index'])->name('index')->defaults('type', $type);
                Route::get('create',       [PartyController::class, 'create'])->name('create')->defaults('type', $type);
                Route::post('/',           [PartyController::class, 'store'])->name('store')->defaults('type', $type);
                Route::get('{party}/edit', [PartyController::class, 'edit'])->name('edit')->defaults('type', $type);
                Route::put('{party}',      [PartyController::class, 'update'])->name('update')->defaults('type', $type);
                Route::delete('{party}',   [PartyController::class, 'destroy'])->name('destroy')->defaults('type', $type);
            });

            Route::prefix("{$type}-groups")->name("{$type}-groups.")->group(function () use ($type) {
                Route::get('/',            [PartyGroupController::class, 'index'])->name('index')->defaults('type', $type);
                Route::get('create',       [PartyGroupController::class, 'create'])->name('create')->defaults('type', $type);
                Route::post('/',           [PartyGroupController::class, 'store'])->name('store')->defaults('type', $type);
                Route::get('{group}/edit', [PartyGroupController::class, 'edit'])->name('edit')->defaults('type', $type);
                Route::put('{group}',      [PartyGroupController::class, 'update'])->name('update')->defaults('type', $type);
                Route::delete('{group}',   [PartyGroupController::class, 'destroy'])->name('destroy')->defaults('type', $type);
            });
        }

        Route::resource('invoice-types', InvoiceTypeController::class)->except(['show']);
        Route::resource('invoices',      InvoiceController::class)->only(['index', 'create', 'store', 'show']);

    });

    Route::resource('tenders', TenderController::class)->except(['show']);

});
