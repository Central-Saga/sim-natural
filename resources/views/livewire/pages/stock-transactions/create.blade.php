<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Services\StockTransactionService;
use Livewire\Attributes\Layout;

new class extends Component {
    #[Layout('components.layouts.app')]

    public $product_id = '';
    public $type = 'in';
    public $quantity = 1;
    public $status = 'completed';
    public $notes = '';
    public $selectedProduct = null;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'type' => 'required|in:in,out,adjustment',
        'quantity' => 'required|integer|min:1',
        'status' => 'required|in:pending,completed,cancelled',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'product_id.required' => 'Produk harus dipilih.',
        'product_id.exists' => 'Produk yang dipilih tidak valid.',
        'type.required' => 'Tipe transaksi harus dipilih.',
        'type.in' => 'Tipe transaksi tidak valid.',
        'quantity.required' => 'Jumlah harus diisi.',
        'quantity.integer' => 'Jumlah harus berupa angka.',
        'quantity.min' => 'Jumlah minimal adalah 1.',
        'status.required' => 'Status harus dipilih.',
        'status.in' => 'Status tidak valid.',
        'notes.max' => 'Catatan maksimal 1000 karakter.',
    ];

    public function updatedProductId($value)
    {
        if ($value) {
            $this->selectedProduct = Product::find($value);
        } else {
            $this->selectedProduct = null;
        }
    }

    public function updatedType($value)
    {
        // Reset quantity when type changes to adjustment
        if ($value === 'adjustment' && $this->selectedProduct) {
            $this->quantity = $this->selectedProduct->stock_quantity;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $stockTransactionService = new StockTransactionService();

            $transaction = $stockTransactionService->createTransaction([
                'product_id' => $this->product_id,
                'type' => $this->type,
                'quantity' => $this->quantity,
                'status' => $this->status,
                'notes' => $this->notes,
            ]);

            session()->flash('message', 'Transaksi stok berhasil dibuat.');
            return $this->redirect(route('stock-transaction.index'), navigate: true);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function with(): array
    {
        return [
            'products' => Product::active()->orderBy('name')->get(),
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
                    {{ __('Create Stock Transaction') }}
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                    {{ __('Add a new stock transaction to the inventory system') }}
                </p>
            </div>
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

    <!-- Flash Messages -->
    @if (session()->has('message'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ session('message') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="p-8">
            <form wire:submit="save" class="space-y-6">
                <!-- Product Selection -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Product') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="product_id" id="product_id"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200">
                        <option value="">{{ __('Select a product...') }}</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ __('Current Stock') }}: {{
                            number_format($product->stock_quantity) }})</option>
                        @endforeach
                    </select>
                    @error('product_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Transaction Type') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="type" id="type"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200">
                        <option value="in">{{ __('Stock In') }} - {{ __('Add stock to inventory') }}</option>
                        <option value="out">{{ __('Stock Out') }} - {{ __('Remove stock from inventory') }}</option>
                        <option value="adjustment">{{ __('Adjustment') }} - {{ __('Adjust stock to specific amount')
                            }}</option>
                    </select>
                    @error('type')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        @if($type === 'adjustment')
                        {{ __('New Stock Amount') }}
                        @else
                        {{ __('Quantity') }}
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="quantity" type="number" id="quantity" min="1"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200"
                        placeholder="@if($type === 'adjustment') {{ __('Enter new stock amount...') }} @else {{ __('Enter quantity...') }} @endif"
                        required>
                    @error('quantity')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Status') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="status" id="status"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200">
                        <option value="completed">{{ __('Completed') }} - {{ __('Transaction is completed') }}
                        </option>
                        <option value="pending">{{ __('Pending') }} - {{ __('Transaction is pending') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }} - {{ __('Transaction is cancelled') }}
                        </option>
                    </select>
                    @error('status')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Notes') }}
                    </label>
                    <textarea wire:model="notes" id="notes" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200"
                        placeholder="{{ __('Enter transaction notes (optional)...') }}"></textarea>
                    @error('notes')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('stock-transaction.index') }}"
                        class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                        wire:navigate>
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <div class="flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>{{ __('Create Transaction') }}</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>