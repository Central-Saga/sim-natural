<?php

use Livewire\Volt\Component;
use App\Models\StockTransaction;
use App\Services\StockTransactionService;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Layout('components.layouts.app')]

    public $search = '';
    public $selectedType = '';
    public $selectedStatus = '';
    public $selectedProduct = '';
    public $selectedUser = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedType' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'selectedProduct' => ['except' => ''],
        'selectedUser' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingSelectedProduct()
    {
        $this->resetPage();
    }

    public function updatingSelectedUser()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'selectedType',
            'selectedStatus',
            'selectedProduct',
            'selectedUser',
            'dateFrom',
            'dateTo'
        ]);
        $this->resetPage();
    }

    public function with(): array
    {
        $stockTransactionService = new StockTransactionService();

        $query = StockTransaction::with(['product', 'user']);

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('product', function($productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('user', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedType) {
            $query->where('type', $this->selectedType);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedProduct) {
            $query->where('product_id', $this->selectedProduct);
        }

        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $transactions = $query->latest()->paginate(15);

        return [
            'transactions' => $transactions,
            'stats' => $stockTransactionService->getStatistics(),
            'recentTransactions' => $stockTransactionService->getRecentTransactions(5),
            'products' => \App\Models\Product::orderBy('name')->get(),
            'users' => \App\Models\User::orderBy('name')->get(),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    {{ __('Stock Transactions') }}
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                    {{ __('Manage and track all stock transactions in the inventory system') }}
                </p>
            </div>
            <a href="{{ route('stock-transaction.create') }}"
                class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                wire:navigate>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Create Transaction') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">{{ __('Total
                        Transactions') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2 truncate">{{
                        $stats['total_transactions'] }}</p>
                </div>
                <div
                    class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">{{ __('Stock Ins') }}
                    </p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2 truncate">{{
                        $stats['stock_ins'] }}</p>
                </div>
                <div
                    class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">{{ __('Stock Outs') }}
                    </p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2 truncate">{{
                        $stats['stock_outs'] }}</p>
                </div>
                <div
                    class="h-12 w-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">{{ __('Today') }}</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400 mt-2 truncate">{{
                        $stats['transactions_today'] }}</p>
                </div>
                <div
                    class="h-12 w-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Filters') }}</h3>
                <button wire:click="clearFilters"
                    class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                    {{ __('Clear All') }}
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Search')
                        }}</label>
                    <input wire:model.live="search" type="text"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="{{ __('Search products, users, notes...') }}">
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Type')
                        }}</label>
                    <select wire:model.live="selectedType"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="in">{{ __('Stock In') }}</option>
                        <option value="out">{{ __('Stock Out') }}</option>
                        <option value="adjustment">{{ __('Adjustment') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status')
                        }}</label>
                    <select wire:model.live="selectedStatus"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Product Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Product')
                        }}</label>
                    <select wire:model.live="selectedProduct"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">{{ __('All Products') }}</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('ID') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Product') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Type') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Quantity') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('User') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Status') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Date') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                #{{ $transaction->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $transaction->product->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $transaction->type_color }}-100 dark:bg-{{ $transaction->type_color }}-900 text-{{ $transaction->type_color }}-800 dark:text-{{ $transaction->type_color }}-200">
                                    {{ $transaction->type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ number_format($transaction->quantity) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $transaction->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $transaction->status_color }}-100 dark:bg-{{ $transaction->status_color }}-900 text-{{ $transaction->status_color }}-800 dark:text-{{ $transaction->status_color }}-200">
                                    {{ $transaction->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('stock-transaction.show', $transaction->id) }}"
                                        class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300"
                                        title="{{ __('View Details') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('stock-transaction.edit', $transaction->id) }}"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        title="{{ __('Edit Transaction') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No transactions found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
</div>