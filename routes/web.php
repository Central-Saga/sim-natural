<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Volt::route('dashboard', 'pages.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::prefix('role')->group(function () {
        Volt::route('/', 'pages.roles.index')->name('role.index');
        Volt::route('/create', 'pages.roles.create')->name('role.create');
        Volt::route('/edit/{id}', 'pages.roles.edit')->name('role.edit');
    });

    Route::prefix('user')->group(function () {
        Volt::route('/', 'pages.users.index')->name('user.index');
        Volt::route('/create', 'pages.users.create')->name('user.create');
        Volt::route('/edit/{id}', 'pages.users.edit')->name('user.edit');
    });

    Route::prefix('category')->group(function () {
        Volt::route('/', 'pages.categories.index')->name('category.index');
        Volt::route('/create', 'pages.categories.create')->name('category.create');
        Volt::route('/edit/{id}', 'pages.categories.edit')->name('category.edit');
    });

    Route::prefix('product')->group(function () {
        Volt::route('/', 'pages.products.index')->name('product.index');
        Volt::route('/create', 'pages.products.create')->name('product.create');
        Volt::route('/edit/{id}', 'pages.products.edit')->name('product.edit');
    });

    Route::prefix('stock-transaction')->group(function () {
        Volt::route('/', 'pages.stock-transactions.index')->name('stock-transaction.index');
        Volt::route('/create', 'pages.stock-transactions.create')->name('stock-transaction.create');
        Volt::route('/{id}', 'pages.stock-transactions.show')->name('stock-transaction.show');
        Volt::route('/edit/{id}', 'pages.stock-transactions.edit')->name('stock-transaction.edit');
        Route::get('/test-export', function () {
            return 'Export route works!';
        })->name('test.export');
        Route::get('/test-controller', [\App\Http\Controllers\StockTransactionExportController::class, 'test'])->name('test.controller');
    });
});

// Test route completely outside middleware
Route::get('/test-simple', function () {
    return 'Simple test works!';
})->withoutMiddleware(['auth', 'web']);

// Test export route outside middleware
Route::get('/test-export-simple', [\App\Http\Controllers\StockTransactionExportController::class, 'test'])->withoutMiddleware(['auth', 'web']);

// Export PDF route with auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/export-stock-pdf', [\App\Http\Controllers\StockTransactionExportController::class, 'exportPdf'])->name('stock-transaction.export-pdf');
});

require __DIR__ . '/auth.php';
