<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productNames = [
            // Bahan Baku
            'Kertas Coklat',
            'Kertas Karton',
            'Kain Kaca',
            'Plat Kayu',
            'Sisitan Bambu',
            'Lem Kayu',
            'Tali Rafia',
            'Paku Kayu',
            'Bambu Betung',
            'Kayu Jati',
            'Kayu Mahoni',
            'Kertas Kraft',

            // Produk Jadi
            'Pot Lontar',
            'Pot Karung',
            'Amy Bambu',
            'AH Kayu',
            'Bh Banana',
            'KDB',
            'KDK',
            'Kukusan',
            'Tas Anyaman',
            'Topi Bambu',
            'Keranjang',
            'Tempat Sampah',
            'Rak Bambu',
            'Meja Kayu',
        ];

        return [
            'name' => $this->faker->randomElement($productNames) . ' ' . $this->faker->randomElement(['Premium', 'Standard', 'Quality', 'Export']),
            'foto_produk' => $this->faker->imageUrl(640, 480, 'products', true),
            'kategori_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the product is in stock.
     */
    public function inStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(1, 1000),
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }
}
