<?php

use Livewire\Volt\Component;
use App\Models\StockTransaction;
use Livewire\Attributes\Layout;

new class extends Component {
    #[Layout('components.layouts.app')]

    public $transaction;
    public $showDeleteModal = false;

    public function mount($id)
    {
        $this->transaction = StockTransaction::with(['product.category', 'user'])->findOrFail($id);
    }

        public function deleteTransaction()
    {
        try {
            DB::transaction(function () {
                $product = $this->transaction->product;

                // Revert the effect of the transaction if it was completed
                if ($this->transaction->status === 'completed') {
                    $revertedStock = match($this->transaction->type) {
                        'in' => $product->stock_quantity - $this->transaction->quantity,
                        'out' => $product->stock_quantity + $this->transaction->quantity,
                        'adjustment' => $this->transaction->quantity_before,
                        default => $product->stock_quantity,
                    };
                    $product->update(['stock_quantity' => max(0, $revertedStock)]);
                }

                // Delete the transaction
                $this->transaction->delete();
            });

            session()->flash('success', __('Transaction deleted successfully.'));
            return $this->redirect(route('stock-transaction.index'));
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to delete transaction.') . ' ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
        $this->dispatch('modal-opened');
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
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
                    class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    {{ __('Transaction Details') }}
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                    {{ __('View detailed information about this stock transaction') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock-transaction.edit', $transaction->id) }}"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    {{ __('Edit Transaction') }}
                </a>
                <button wire:click="confirmDelete"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    {{ __('Delete Transaction') }}
                </button>
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
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
                                {{ __('Product ID') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                #{{ $transaction->product->id }}
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Quick Actions') }}
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('stock-transaction.edit', $transaction->id) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            {{ __('Edit Transaction') }}
                        </a>
                        <a href="{{ route('product.edit', $transaction->product->id) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
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

    <!-- Delete Confirmation Modal -->
    <div style="display: {{ $showDeleteModal ? 'block' : 'none' }};">
        <div class="fixed inset-0 backdrop-blur-md transition-opacity z-50" wire:click="cancelDelete"></div>

        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">
                                {{ __('Delete Transaction') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Are you sure you want to delete this transaction?') }}
                                </p>
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ __('Transaction Details') }}:
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">{{ __('Product') }}:</span> {{
                                        $transaction->product->name }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">{{ __('Type') }}:</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaction->type_color }}">
                                            {{ $transaction->type_label }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">{{ __('Quantity') }}:</span> {{
                                        number_format($transaction->quantity) }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        <span class="font-medium">{{ __('Status') }}:</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaction->status_color }}">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </p>
                                </div>
                                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                    {{ __('This action cannot be undone.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            class="inline-flex w-full justify-center rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                            wire:click="deleteTransaction">
                            {{ __('Delete') }}
                        </button>
                        <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto"
                            wire:click="cancelDelete">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>