<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\StockTransaction;
use App\Services\StockTransactionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing products and users
        $products = Product::all();
        $users = User::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $this->command->info('Creating stock transactions...');

        // Create initial stock for all products (stock in transactions)
        foreach ($products as $product) {
            // Create initial stock in transaction
            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => $users->random()->id,
                'type' => 'in',
                'quantity' => rand(50, 200),
                'quantity_before' => 0,
                'quantity_after' => rand(50, 200),
                'status' => 'completed',
                'notes' => 'Initial stock setup',
                'created_at' => now()->subMonths(6),
                'updated_at' => now()->subMonths(6),
            ]);

            // Update product stock
            $product->update(['stock_quantity' => rand(50, 200)]);
        }

        // Create various transactions over the last 6 months
        $transactionTypes = ['in', 'out', 'adjustment'];
        $statuses = ['completed', 'pending', 'cancelled'];

        // Create 50-100 random transactions
        $transactionCount = rand(50, 100);

        for ($i = 0; $i < $transactionCount; $i++) {
            $product = $products->random();
            $user = $users->random();
            $type = $transactionTypes[array_rand($transactionTypes)];
            $status = $statuses[array_rand($statuses)];

            // Generate realistic transaction data
            $quantity = match ($type) {
                'in' => rand(10, 50),
                'out' => rand(1, min(20, $product->stock_quantity)),
                'adjustment' => rand(0, 100),
                default => rand(1, 50),
            };

            $quantityBefore = $product->stock_quantity;

            // Calculate quantity after based on type
            $quantityAfter = match ($type) {
                'in' => $quantityBefore + $quantity,
                'out' => max(0, $quantityBefore - $quantity),
                'adjustment' => $quantity,
                default => $quantityBefore,
            };

            // Create transaction
            $transaction = StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'status' => $status,
                'notes' => $this->generateTransactionNote($type, $quantity),
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now()->subDays(rand(1, 180)),
            ]);

            // Update product stock if transaction is completed
            if ($status === 'completed') {
                $product->update(['stock_quantity' => $quantityAfter]);
            }
        }

        // Create some recent transactions (last 7 days)
        for ($i = 0; $i < 10; $i++) {
            $product = $products->random();
            $user = $users->random();
            $type = $transactionTypes[array_rand($transactionTypes)];

            $quantity = match ($type) {
                'in' => rand(5, 25),
                'out' => rand(1, min(10, $product->stock_quantity)),
                'adjustment' => rand(0, 50),
                default => rand(1, 20),
            };

            $quantityBefore = $product->stock_quantity;
            $quantityAfter = match ($type) {
                'in' => $quantityBefore + $quantity,
                'out' => max(0, $quantityBefore - $quantity),
                'adjustment' => $quantity,
                default => $quantityBefore,
            };

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'status' => 'completed',
                'notes' => $this->generateTransactionNote($type, $quantity),
                'created_at' => now()->subDays(rand(0, 7)),
                'updated_at' => now()->subDays(rand(0, 7)),
            ]);

            // Update product stock
            $product->update(['stock_quantity' => $quantityAfter]);
        }

        $this->command->info('Stock transactions created successfully!');
    }

    /**
     * Generate realistic transaction notes based on type and quantity
     */
    private function generateTransactionNote(string $type, int $quantity): string
    {
        $notes = match ($type) {
            'in' => [
                "Stock masuk dari supplier - {$quantity} unit",
                "Penerimaan barang baru - {$quantity} unit",
                "Restock produk - {$quantity} unit",
                "Pengiriman dari gudang pusat - {$quantity} unit",
                "Pembelian stok - {$quantity} unit",
            ],
            'out' => [
                "Penjualan - {$quantity} unit",
                "Pengiriman ke customer - {$quantity} unit",
                "Transfer ke outlet lain - {$quantity} unit",
                "Sample produk - {$quantity} unit",
                "Retur ke supplier - {$quantity} unit",
            ],
            'adjustment' => [
                "Penyesuaian stok - {$quantity} unit",
                "Stock opname - {$quantity} unit",
                "Koreksi stok - {$quantity} unit",
                "Penyesuaian inventory - {$quantity} unit",
                "Audit stok - {$quantity} unit",
            ],
            default => "Transaksi stok - {$quantity} unit",
        };

        return $notes[array_rand($notes)];
    }
}
