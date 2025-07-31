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

    // Role management - hanya Admin
    Route::prefix('role')->middleware(['permission:mengelola role'])->group(function () {
        Volt::route('/', 'pages.roles.index')->name('role.index');
        Volt::route('/create', 'pages.roles.create')->name('role.create');
        Volt::route('/edit/{id}', 'pages.roles.edit')->name('role.edit');
    });

    // User management - hanya Admin
    Route::prefix('user')->middleware(['permission:mengelola user'])->group(function () {
        Volt::route('/', 'pages.users.index')->name('user.index');
        Volt::route('/create', 'pages.users.create')->name('user.create');
        Volt::route('/edit/{id}', 'pages.users.edit')->name('user.edit');
    });

    // Category management - hanya Admin
    Route::prefix('category')->middleware(['permission:mengelola kategori'])->group(function () {
        Volt::route('/', 'pages.categories.index')->name('category.index');
        Volt::route('/create', 'pages.categories.create')->name('category.create');
        Volt::route('/edit/{id}', 'pages.categories.edit')->name('category.edit');
    });

    // Product management - Admin dan Akuntan
    Route::prefix('product')->middleware(['permission:mengelola produk'])->group(function () {
        Volt::route('/', 'pages.products.index')->name('product.index');
        Volt::route('/create', 'pages.products.create')->name('product.create');
        Volt::route('/edit/{id}', 'pages.products.edit')->name('product.edit');
    });

    // Stock transaction management - Admin dan Karyawan Gudang
    Route::prefix('stock-transaction')->middleware(['permission:melihat transaksi stok'])->group(function () {
        Volt::route('/', 'pages.stock-transactions.index')->name('stock-transaction.index');
        Volt::route('/{id}', 'pages.stock-transactions.show')->name('stock-transaction.show');
    });

    // Stock transaction create/edit - hanya Admin dan Karyawan Gudang
    Route::prefix('stock-transaction')->middleware(['permission:mengelola transaksi stok'])->group(function () {
        Volt::route('/create', 'pages.stock-transactions.create')->name('stock-transaction.create');
        Volt::route('/edit/{id}', 'pages.stock-transactions.edit')->name('stock-transaction.edit');
    });

    // Export PDF - hanya Admin dan Akuntan
    Route::middleware(['permission:mencetak laporan'])->group(function () {
        Route::get('/export-stock-pdf', [\App\Http\Controllers\StockTransactionExportController::class, 'exportPdf'])->name('stock-transaction.export-pdf');
    });
});

// Test route completely outside middleware
Route::get('/test-simple', function () {
    return 'Simple test works!';
})->withoutMiddleware(['auth', 'web']);

// Test export route outside middleware
Route::get('/test-export-simple', [\App\Http\Controllers\StockTransactionExportController::class, 'test'])->withoutMiddleware(['auth', 'web']);

require __DIR__ . '/auth.php';
