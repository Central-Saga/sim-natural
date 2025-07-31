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
            'Elektronik' => 'Produk elektronik dan gadget',
            'Fashion' => 'Pakaian dan aksesoris fashion',
            'Makanan & Minuman' => 'Produk makanan dan minuman',
            'Kesehatan & Kecantikan' => 'Produk kesehatan dan kecantikan',
            'Rumah Tangga' => 'Peralatan dan kebutuhan rumah tangga',
            'Olahraga' => 'Peralatan dan pakaian olahraga',
            'Buku & Alat Tulis' => 'Buku dan perlengkapan alat tulis',
            'Otomotif' => 'Suku cadang dan aksesoris kendaraan',
            'Mainan & Hobi' => 'Mainan dan produk hobi',
            'Pertanian' => 'Produk pertanian dan perkebunan',
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
