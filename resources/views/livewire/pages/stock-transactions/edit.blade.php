<?php

use Livewire\Volt\Component;
use App\Models\StockTransaction;
use App\Models\Product;
use App\Services\StockTransactionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

new class extends Component {
    #[Layout('components.layouts.app')]

    public $transaction;
    public $productId;
    public $type;
    public $quantity;
    public $status;
    public $notes;
    public $currentStock;

    #[Rule('required|exists:products,id')]
    public $newProductId;

    #[Rule('required|in:in,out,adjustment')]
    public $newType;

    #[Rule('required|integer|min:1')]
    public $newQuantity;

    #[Rule('required|in:pending,completed,cancelled')]
    public $newStatus;

    #[Rule('nullable|string|max:500')]
    public $newNotes;

    #[Rule('nullable|integer|min:0')]
    public $newStockAmount;

    public function mount($id)
    {
        $this->transaction = StockTransaction::findOrFail($id);
        $this->productId = $this->transaction->product_id;
        $this->type = $this->transaction->type;
        $this->quantity = $this->transaction->quantity;
        $this->status = $this->transaction->status;
        $this->notes = $this->transaction->notes;
        $this->currentStock = $this->transaction->product->stock_quantity;

        // Initialize form fields
        $this->newProductId = $this->transaction->product_id;
        $this->newType = $this->transaction->type;
        $this->newQuantity = $this->transaction->quantity;
        $this->newStatus = $this->transaction->status;
        $this->newNotes = $this->transaction->notes;
        $this->newStockAmount = $this->transaction->type === 'adjustment' ? $this->transaction->quantity_after : null;
    }

    public function updatedNewProductId()
    {
        if ($this->newProductId) {
            $product = Product::find($this->newProductId);
            $this->currentStock = $product ? $product->stock_quantity : 0;
        }
    }

    public function updatedNewType()
    {
        if ($this->newType === 'adjustment') {
            $this->newStockAmount = $this->currentStock;
        } else {
            $this->newStockAmount = null;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $service = new StockTransactionService();

            // Calculate new quantity after based on type
            $quantityAfter = match($this->newType) {
                'in' => $this->currentStock + $this->newQuantity,
                'out' => max(0, $this->currentStock - $this->newQuantity),
                'adjustment' => $this->newStockAmount,
                default => $this->currentStock,
            };

            // Update transaction
            $this->transaction->update([
                'product_id' => $this->newProductId,
                'type' => $this->newType,
                'quantity' => $this->newQuantity,
                'quantity_before' => $this->currentStock,
                'quantity_after' => $quantityAfter,
                'status' => $this->newStatus,
                'notes' => $this->newNotes,
            ]);

            // Update product stock if status is completed
            if ($this->newStatus === 'completed') {
                $product = Product::find($this->newProductId);
                if ($product) {
                    $product->update(['stock_quantity' => $quantityAfter]);
                }
            }

            session()->flash('success', __('Stock transaction updated successfully.'));
            return $this->redirect(route('stock-transaction.index'));

        } catch (\Exception $e) {
            session()->flash('error', __('Failed to update stock transaction.'));
        }
    }

    public function with(): array
    {
        return [
            'products' => Product::orderBy('name')->get(),
            'transactionTypes' => [
                'in' => __('Stock In'),
                'out' => __('Stock Out'),
                'adjustment' => __('Adjustment'),
            ],
            'statuses' => [
                'pending' => __('Pending'),
                'completed' => __('Completed'),
                'cancelled' => __('Cancelled'),
            ],
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
                    {{ __('Edit Stock Transaction') }}
                </h1>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                    {{ __('Update stock transaction information') }}
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
    @if (session('success'))
    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    @if (session('error'))
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

    <!-- Current Transaction Info -->
    <div class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                {{ __('Current Transaction Details') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Product') }}
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $transaction->product->name }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Type') }}
                    </label>
                    <span
                        class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->type_color }}">
                        {{ $transaction->type_label }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Quantity') }}
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $transaction->quantity }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Status') }}
                    </label>
                    <span
                        class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status_color }}">
                        {{ $transaction->status_label }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="p-8">
            <form wire:submit="save" class="space-y-6">
                <!-- Product Selection -->
                <div>
                    <label for="newProductId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Product') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="newProductId" id="newProductId"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200">
                        <option value="">{{ __('Select a product...') }}</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $product->id == $newProductId ? 'selected' : '' }}>
                            {{ $product->name }} ({{ __('Current Stock') }}: {{ number_format($product->stock_quantity)
                            }})
                        </option>
                        @endforeach
                    </select>
                    @error('newProductId')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Stock Display -->
                @if($newProductId)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                <span class="font-semibold">{{ __('Current Stock') }}:</span> {{
                                number_format($currentStock) }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Transaction Type -->
                <div>
                    <label for="newType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Transaction Type') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="newType" id="newType"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200">
                        <option value="in">{{ __('Stock In') }} - {{ __('Add stock to inventory') }}</option>
                        <option value="out">{{ __('Stock Out') }} - {{ __('Remove stock from inventory') }}</option>
                        <option value="adjustment">{{ __('Adjustment') }} - {{ __('Adjust stock to specific amount') }}
                        </option>
                    </select>
                    @error('newType')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="newQuantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        @if($newType === 'adjustment')
                        {{ __('New Stock Amount') }}
                        @else
                        {{ __('Quantity') }}
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" wire:model="newQuantity" id="newQuantity" min="1"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200"
                        placeholder="@if($newType === 'adjustment') {{ __('Enter new stock amount...') }} @else {{ __('Enter quantity...') }} @endif">
                    @error('newQuantity')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="newStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Status') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="newStatus" id="newStatus"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200">
                        <option value="completed">{{ __('Completed') }} - {{ __('Transaction is completed') }}</option>
                        <option value="pending">{{ __('Pending') }} - {{ __('Transaction is pending') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }} - {{ __('Transaction is cancelled') }}</option>
                    </select>
                    @error('newStatus')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="newNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Notes') }}
                    </label>
                    <textarea wire:model="newNotes" id="newNotes" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 dark:focus:border-emerald-400 transition-colors duration-200"
                        placeholder="{{ __('Enter transaction notes (optional)...') }}"></textarea>
                    @error('newNotes')
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
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            <span>{{ __('Update Transaction') }}</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>