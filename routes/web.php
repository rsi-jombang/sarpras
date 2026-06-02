<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Models\Unit;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,sarpras'])->group(function () {
    Route::get('/units', [UnitController::class, 'index'])->name('units.index')->can('viewAny', Unit::class);
    Route::post('/units', [UnitController::class, 'store'])->name('units.store')->can('create', Unit::class);
    Route::get('/units/{unit}', [UnitController::class, 'show'])->name('units.show')->can('view', Unit::class);
    Route::put('/units/{unit}', [UnitController::class, 'update'])->name('units.update')->can('update', Unit::class);
    Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy')->can('delete', Unit::class);
    Route::patch('/units/{unit}/toggle', [UnitController::class, 'toggleStatus'])->name('units.toggle')->can('update', Unit::class);

    Route::group(['prefix' => 'category', 'as' => 'category.', 'controller' => CategoryController::class], function(){
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{category}', 'show')->name('show');
        Route::put('/{category}', 'update')->name('update');
        Route::delete('/{category}', 'destroy')->name('destroy');
        Route::patch('/{category}/toggle', 'toggleStatus')->name('toggle');
    });

    // Route Items biasa
    Route::group(['prefix' => 'items', 'as' => 'items.', 'controller' => ItemController::class], function(){
        Route::get('/','index')->name('index');
        Route::get('/trash','trash')->name('trash');
        Route::get('/create','create')->name('create');
        Route::post('/','store')->name('store');
        Route::get('/{item}','show')->name('show');
        Route::get('/{item}/edit','edit')->name('edit');
        Route::post('/{item}','update')->name('update');
        Route::delete('/{item}','destroy')->name('destroy');
    });

    // Route Items Api
    Route::group(['prefix' => 'api/items', 'as' => 'api.items.', 'controller' => App\Http\Controllers\Api\ItemApiController::class], function(){
        // DataTables & helpers
        Route::get('/','index')->name('index');
        Route::get('/stats','stats')->name('stats');
        Route::get('/categories','categories')->name('categories');
        // Trash
        Route::get('/trash','trash')->name('trash');
        // CRUD
        Route::post('/','store')->name('store');
        Route::get('/{item}','show')->name('show');
        Route::post('/{item}','update')->name('update'); // POST karena ada file upload
        Route::delete('/{item}','destroy')->name('destroy');
        // Stok & pergerakan per item
        Route::get('/{item}/stocks','stocks')->name('stocks');
        Route::get('/{item}/movements','movements')->name('movements');
        // Trash actions
        Route::post('/{id}/restore','restore')->name('restore');
        Route::delete('/{id}/force-delete','forceDelete')->name('force-delete');
    });

    Route::group(['prefix' => 'purchase-orders', 'as' => 'purchase-orders.', 'controller' => App\Http\Controllers\PurchaseOrderController::class], function(){
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

    Route::group(['prefix' => 'api/purchase-orders', 'as' => 'api.purchase-orders.', 'controller' => App\Http\Controllers\Api\PurchaseOrderApiController::class], function(){
        // DataTables & stats
        Route::get('/','index')->name('index');
        Route::get('/stats','stats')->name('stats');
        Route::get('/items','items')->name('items');

        // CRUD
        Route::post('/', 'store')->name('store');
        Route::get('/{purchaseOrder}','show')->name('show');
        Route::put('/{purchaseOrder}', 'update')->name('update');

        // Aksi status
        Route::post('/{purchaseOrder}/approve', 'approve')->name('approve');
        Route::post('/{purchaseOrder}/receive', 'receive')->name('receive');
        Route::post('/{purchaseOrder}/cancel', 'cancel')->name('cancel');
    });
});

require __DIR__.'/auth.php';
