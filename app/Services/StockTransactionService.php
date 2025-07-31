<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockTransactionService
{
    /**
     * Create a stock transaction and update product stock
     */
    public function createTransaction(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $quantityBefore = $product->stock_quantity;

            // Calculate new quantity based on transaction type
            $quantityChange = match ($data['type']) {
                'in' => $data['quantity'],
                'out' => -$data['quantity'],
                'adjustment' => $data['quantity'] - $quantityBefore,
                default => 0,
            };

            $quantityAfter = $quantityBefore + $quantityChange;

            // Validate stock for out transactions
            if ($data['type'] === 'out' && $quantityAfter < 0) {
                throw new \Exception('Stok tidak mencukupi untuk transaksi keluar.');
            }

            // Create transaction record
            $transaction = StockTransaction::create([
                'product_id' => $data['product_id'],
                'user_id' => $data['user_id'] ?? Auth::id(),
                'type' => $data['type'],
                'quantity' => abs($data['quantity']),
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'status' => $data['status'] ?? 'completed',
                'notes' => $data['notes'] ?? null,
            ]);

            // Update product stock
            $product->update(['stock_quantity' => $quantityAfter]);

            return $transaction;
        });
    }

    /**
     * Get stock transaction statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_transactions' => StockTransaction::count(),
            'transactions_today' => StockTransaction::whereDate('created_at', today())->count(),
            'transactions_this_month' => StockTransaction::whereMonth('created_at', now()->month)->count(),
            'stock_ins' => StockTransaction::ofType('in')->count(),
            'stock_outs' => StockTransaction::ofType('out')->count(),
            'adjustments' => StockTransaction::ofType('adjustment')->count(),
            'pending_transactions' => StockTransaction::ofStatus('pending')->count(),
            'completed_transactions' => StockTransaction::ofStatus('completed')->count(),
            'cancelled_transactions' => StockTransaction::ofStatus('cancelled')->count(),
        ];
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return StockTransaction::with(['product', 'user'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get transactions by product
     */
    public function getTransactionsByProduct(int $productId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return StockTransaction::with(['user'])
            ->where('product_id', $productId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get transactions by user
     */
    public function getTransactionsByUser(int $userId, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return StockTransaction::with(['product'])
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Cancel a transaction
     */
    public function cancelTransaction(int $transactionId): bool
    {
        return DB::transaction(function () use ($transactionId) {
            $transaction = StockTransaction::findOrFail($transactionId);

            if ($transaction->status !== 'completed') {
                throw new \Exception('Hanya transaksi yang sudah selesai yang dapat dibatalkan.');
            }

            $product = $transaction->product;
            $currentStock = $product->stock_quantity;

            // Reverse the transaction
            $quantityChange = match ($transaction->type) {
                'in' => -$transaction->quantity,
                'out' => $transaction->quantity,
                'adjustment' => $transaction->quantity_before - $currentStock,
                default => 0,
            };

            $newStock = $currentStock + $quantityChange;

            // Validate stock for cancellation
            if ($newStock < 0) {
                throw new \Exception('Tidak dapat membatalkan transaksi karena stok akan menjadi negatif.');
            }

            // Update transaction status
            $transaction->update(['status' => 'cancelled']);

            // Update product stock
            $product->update(['stock_quantity' => $newStock]);

            return true;
        });
    }
}
