<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Models\Request;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::middleware(['auth', 'role:admin,sarpras'])->group(function () {
Route::middleware(['auth'])->group(function () {
    Route::get('/units', [UnitController::class, 'index'])->name('units.index')->can('viewAny', Unit::class);
    Route::post('/units', [UnitController::class, 'store'])->name('units.store')->can('create', Unit::class);
    Route::get('/units/{unit}', [UnitController::class, 'show'])->name('units.show')->can('view', Unit::class);
    Route::put('/units/{unit}', [UnitController::class, 'update'])->name('units.update')->can('update', Unit::class);
    Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy')->can('delete', Unit::class);
    Route::patch('/units/{unit}/toggle', [UnitController::class, 'toggleStatus'])->name('units.toggle')->can('update', Unit::class);

    Route::get('/users', [UserController::class, 'index'])->name('users.index')->can('viewAny', User::class);
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->can('create', User::class);
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->can('view', 'user');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->can('update', 'user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->can('delete', 'user');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle')->can('update', User::class);

    Route::group(['prefix' => 'api/dashboard', 'as' => 'api.dashboard.', 'controller' => App\Http\Controllers\Api\DashboardApiController::class], function () {
        // Unit
        Route::get('/unit/stats', 'unitStats')->name('unit.stats');
        Route::get('/unit/charts', 'unitCharts')->name('unit.charts');
        Route::get('/unit/recent-requests', 'unitRecentRequests')->name('unit.recent');

        // Sarpras
        Route::get('/sarpras/stats', 'sarprasStats')->name('sarpras.stats');
        Route::get('/sarpras/charts', 'sarprasCharts')->name('sarpras.charts');
        Route::get('/sarpras/activity', 'sarprasActivity')->name('sarpras.activity');

        // Admin
        Route::get('/admin/stats', 'adminStats')->name('admin.stats');
        Route::get('/admin/charts', 'adminCharts')->name('admin.charts');
    });

    Route::group(['prefix' => 'category', 'as' => 'category.', 'controller' => CategoryController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{category}', 'show')->name('show');
        Route::put('/{category}', 'update')->name('update');
        Route::delete('/{category}', 'destroy')->name('destroy');
        Route::patch('/{category}/toggle', 'toggleStatus')->name('toggle');
    });

    // Route Items biasa
    Route::group(['prefix' => 'items', 'as' => 'items.', 'controller' => ItemController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/trash', 'trash')->name('trash');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{item}', 'show')->name('show');
        Route::get('/{item}/edit', 'edit')->name('edit');
        Route::post('/{item}', 'update')->name('update');
        Route::delete('/{item}', 'destroy')->name('destroy');
    });

    // Route Items Api
    Route::group(['prefix' => 'api/items', 'as' => 'api.items.', 'controller' => App\Http\Controllers\Api\ItemApiController::class], function () {
        // DataTables & helpers
        Route::get('/', 'index')->name('index');
        Route::get('/stats', 'stats')->name('stats');
        Route::get('/categories', 'categories')->name('categories');
        // Trash
        Route::get('/trash', 'trash')->name('trash');
        // CRUD
        Route::post('/', 'store')->name('store');
        Route::get('/{item}', 'show')->name('show');
        Route::post('/{item}', 'update')->name('update'); // POST karena ada file upload
        Route::delete('/{item}', 'destroy')->name('destroy');
        // Stok & pergerakan per item
        Route::get('/{item}/stocks', 'stocks')->name('stocks');
        Route::get('/{item}/movements', 'movements')->name('movements');
        // Trash actions
        Route::post('/{id}/restore', 'restore')->name('restore');
        Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
    });

    Route::group(['prefix' => 'purchase-orders', 'as' => 'purchase-orders.', 'controller' => App\Http\Controllers\PurchaseOrderController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{purchaseOrder}', 'show')->name('show');
        Route::get('/trash', 'trash')->name('trash');
        Route::get('/{purchaseOrder}/edit', 'edit')->name('edit');
        Route::post('/{purchaseOrder}', 'update')->name('update'); // Use POST with _method=PUT for file uploads
        Route::delete('/{purchaseOrder}', 'destroy')->name('destroy');
        // Route::patch('/{purchaseOrder}/toggle', 'toggleStatus')->name('toggle');
    });

    Route::group(['prefix' => 'api/purchase-orders', 'as' => 'api.purchase-orders.', 'controller' => App\Http\Controllers\Api\PurchaseOrderApiController::class], function () {
        // DataTables & stats
        Route::get('/', 'index')->name('index');
        Route::get('/stats', 'stats')->name('stats');
        Route::get('/items', 'items')->name('items');

        // CRUD
        Route::post('/', 'store')->name('store');
        Route::get('/{purchaseOrder}', 'show')->name('show');
        Route::put('/{purchaseOrder}', 'update')->name('update');

        // Aksi status
        Route::post('/{purchaseOrder}/approve', 'approve')->name('approve');
        Route::post('/{purchaseOrder}/receive', 'receive')->name('receive');
        Route::post('/{purchaseOrder}/cancel', 'cancel')->name('cancel');
    });

    // Route Biasah
    Route::group(['prefix' => 'requests', 'as' => 'requests.', 'controller' => App\Http\Controllers\RequestController::class], function () {
        Route::get('/', 'index')->name('index')->can('viewAny', Request::class);
        Route::get('/create', 'create')->name('create')->can('create', Request::class);
        Route::post('/', 'store')->name('store')->can('create', Request::class);
        Route::get('/{request}', 'show')->name('show')->can('view', 'request');
        Route::get('/{request}/pdf', 'pdf')->name('pdf')->can('view', 'request');
        Route::get('/{request}/review', 'reviewForm')->name('review.form')->can('review', 'request');

        Route::post('/{request}/review', 'review')->name('review')->can('review', 'request');
        Route::post('/{request}/fulfill', 'fulfill')->name('fulfill')->can('fulfill', 'request');
        Route::post('/{request}/complete', 'complete')->name('complete')->can('complete', 'request');
        Route::post('/{request}/cancel', 'cancel')->name('cancel')->can('cancel', 'request');
    });

    // Route API Request Item
    Route::group(['prefix' => 'api/requests', 'as' => 'api.requests.', 'controller' => App\Http\Controllers\Api\RequestApiController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/stats', 'stats')->name('stats');
        Route::get('/items', 'items')->name('items');
        Route::get('/units', 'units')->name('units');

        // Aksi status via AJAX
        Route::post('/{request}/fulfill', 'fulfill')->name('fulfill');
        Route::post('/{request}/complete', 'complete')->name('complete');
        Route::post('/{request}/cancel', 'cancel')->name('cancel');
    });

    // Route Stock Opname
    Route::group(['prefix' => 'stock-opname', 'as' => 'stock-opname.', 'controller' => App\Http\Controllers\StockOpnameController::class, 'middleware' => 'can:viewAny,App\Models\StockOpname'], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{stockOpname}', 'show')->name('show');
        Route::post('/{stockOpname}/items', 'updateItems')->name('update-items');
        Route::get('/{stockOpname}/close', 'closeConfirm')->name('close.confirm');
        Route::post('/{stockOpname}/close', 'close')->name('close');
    });

    // Route Stock Opname Api
    Route::group(['prefix' => 'api/stock-opname', 'as' => 'api.stock-opname.', 'controller' => App\Http\Controllers\Api\StockOpnameApiController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/history', 'history')->name('history');
        Route::get('/stats', 'stats')->name('stats');
        Route::get('/{stockOpname}/items', 'items')->name('items');
        Route::delete('/{stockOpname}', 'destroy')->name('destroy');
    });

    // Route Stock Adjustment
    Route::group(['prefix' => 'stock-adjustments', 'as' => 'stock-adjustments.', 'controller' => App\Http\Controllers\StockAdjustmentController::class], function () {
        Route::get('/', 'index')->name('index')->can('viewAny', App\Models\StockAdjustment::class);
        Route::get('/create', 'create')->name('create')->can('create', App\Models\StockAdjustment::class);
        Route::post('/', 'store')->name('store')->can('create', App\Models\StockAdjustment::class);
        Route::get('/{stockAdjustment}', 'show')->name('show')->can('view', 'stockAdjustment');
        Route::delete('/{stockAdjustment}', 'destroy')->name('destroy')->can('delete', 'stockAdjustment');
    });

    // Route Stock Adjustment Api
    Route::group(['prefix' => 'api/stock-adjustments', 'as' => 'api.stock-adjustments.', 'controller' => App\Http\Controllers\Api\StockAdjustmentApiController::class], function () {
        Route::get('/', 'index')->name('index');
    });

    // Route Report
    Route::group(['prefix' => 'reports', 'as' => 'reports.', 'controller' => App\Http\Controllers\ReportController::class], function () {
        // Halaman utama laporan
        Route::get('/', 'index')->name('index');

        // Laporan Permintaan
        Route::get('/requests', 'requestsPreview')->name('requests');
        Route::get('/requests/pdf', 'requestsPdf')->name('requests.pdf');
        Route::get('/requests/excel', 'requestsExcel')->name('requests.excel');

        // Laporan Stok Barang
        Route::get('/stocks', 'stocksPreview')->name('stocks');
        Route::get('/stocks/pdf', 'stocksPdf')->name('stocks.pdf');
        Route::get('/stocks/excel', 'stocksExcel')->name('stocks.excel');

        // Laporan Purchase Order
        Route::get('/purchase-orders', 'purchaseOrdersPreview')->name('purchase-orders');
        Route::get('/purchase-orders/pdf', 'purchaseOrdersPdf')->name('purchase-orders.pdf');
        Route::get('/purchase-orders/excel', 'purchaseOrdersExcel')->name('purchase-orders.excel');

        // Laporan Stok Opname
        Route::get('/opname', 'opnamePreview')->name('opname');
        Route::get('/opname/pdf', 'opnamePdf')->name('opname.pdf');
        Route::get('/opname/excel', 'opnameExcel')->name('opname.excel');
    });
});

require __DIR__ . '/auth.php';
