<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\StockTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockTransaction>
 */
class StockTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $type = $this->faker->randomElement(['in', 'out', 'adjustment']);
        $quantity = $this->faker->numberBetween(1, 100);
        $quantityBefore = $product->stock_quantity;

        // Calculate quantity after based on type
        $quantityAfter = match ($type) {
            'in' => $quantityBefore + $quantity,
            'out' => max(0, $quantityBefore - $quantity),
            'adjustment' => $quantity,
            default => $quantityBefore,
        };

        return [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'status' => $this->faker->randomElement(['completed', 'pending', 'cancelled']),
            'notes' => $this->faker->optional(0.7)->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the transaction is a stock in.
     */
    public function stockIn(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'in',
        ]);
    }

    /**
     * Indicate that the transaction is a stock out.
     */
    public function stockOut(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'out',
        ]);
    }

    /**
     * Indicate that the transaction is an adjustment.
     */
    public function adjustment(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'adjustment',
        ]);
    }

    /**
     * Indicate that the transaction is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transaction is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Create a transaction with specific product.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn(array $attributes) => [
            'product_id' => $product->id,
            'quantity_before' => $product->stock_quantity,
        ]);
    }

    /**
     * Create a transaction with specific user.
     */
    public function byUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
