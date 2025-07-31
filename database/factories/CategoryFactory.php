<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Bahan Baku Kertas' => 'Bahan baku kertas seperti kertas coklat, kertas karton, dan sejenisnya',
            'Bahan Baku Tekstil' => 'Bahan baku tekstil seperti kain kaca dan sejenisnya',
            'Bahan Baku Kayu' => 'Bahan baku kayu seperti plat kayu dan sejenisnya',
            'Bahan Baku Bambu' => 'Bahan baku bambu seperti sisitan bambu dan sejenisnya',
            'Bahan Baku Perekat' => 'Bahan baku perekat seperti lem dan sejenisnya',
            'Produk Jadi Pot' => 'Produk jadi berupa pot seperti Pot lontar, Pot karung, dan sejenisnya',
            'Produk Jadi Kerajinan' => 'Produk jadi kerajinan seperti Amy, AH, Bh banana, dan sejenisnya',
            'Produk Jadi Dapur' => 'Produk jadi untuk dapur seperti KDB, KDK, Kukusan, dan sejenisnya',
            'Bahan Baku Lainnya' => 'Bahan baku lainnya yang belum dikategorikan',
            'Produk Jadi Lainnya' => 'Produk jadi lainnya yang belum dikategorikan',
        ];

        $category = $this->faker->unique()->randomElement(array_keys($categories));

        return [
            'name' => $category,
            'description' => $categories[$category],
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
