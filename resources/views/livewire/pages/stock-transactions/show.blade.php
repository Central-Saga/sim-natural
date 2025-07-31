<?php

use Livewire\Volt\Component;
use App\Models\StockTransaction;
use Livewire\Attributes\Layout;

new class extends Component {
    #[Layout('components.layouts.app')]

    public $transaction;

    public function mount($id)
    {
        $this->transaction = StockTransaction::with(['product.category', 'user'])->findOrFail($id);
    }

    public function with(): array
    {
        return [
            'transaction' => $this->transaction,
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    {{ __('Transaction Details') }}
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                    {{ __('View detailed information about this stock transaction') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock-transaction.edit', $transaction->id) }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    {{ __('Edit Transaction') }}
                </a>
                <a href="{{ route('stock-transaction.index') }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                    wire:navigate>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Transactions') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Transaction Status Banner -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $transaction->status_color }}">
                            {{ $transaction->status_label }}
                        </span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ __('Transaction') }} #{{ $transaction->id }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Created on') }} {{ $transaction->created_at->format('d F Y \a\t H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $transaction->type_color }}">
                        {{ $transaction->type_label }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Transaction Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Product Information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Product Name') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->product->name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Product Code') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->product->code ?? __('N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Category') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->product->category->name ?? __('N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Current Stock') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ number_format($transaction->product->stock_quantity) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Transaction Details') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Transaction Type') }}
                            </label>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->type_color }}">
                                    {{ $transaction->type_label }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Quantity') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ number_format($transaction->quantity) }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Stock Before') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ number_format($transaction->quantity_before) }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Stock After') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ number_format($transaction->quantity_after) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($transaction->notes)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Notes') }}
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $transaction->notes }}
                    </p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('User Information') }}
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Created By') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->user->name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Email') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->user->email }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('User Status') }}
                            </label>
                            <div class="mt-1">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->user->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ ucfirst($transaction->user->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Timestamps') }}
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Created At') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Updated At') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $transaction->updated_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Quick Actions') }}
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('stock-transaction.edit', $transaction->id) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            {{ __('Edit Transaction') }}
                        </a>
                        <a href="{{ route('product.edit', $transaction->product->id) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            {{ __('View Product') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>