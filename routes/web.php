<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
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
});

require __DIR__ . '/auth.php';
