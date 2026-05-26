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

    Route::group(['prefix' => 'items', 'as' => 'items.', 'controller' => ItemController::class], function(){
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{item}', 'show')->name('show');
        Route::get('/{item}/edit', 'edit')->name('edit');
        Route::post('/{item}', 'update')->name('update'); // Use POST with _method=PUT for file uploads
        Route::delete('/{item}', 'destroy')->name('destroy');
        Route::patch('/{item}/toggle', 'toggleStatus')->name('toggle');
        Route::get('/{item}/stocks', 'stocks')->name('stocks');
        Route::patch('/{item}/minimum-stock', 'updateMinimumStock')->name('minimum-stock');
    });
});

require __DIR__.'/auth.php';
