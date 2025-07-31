<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;

class SeedCategoriesAndProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:categories-products {--categories-only : Seed only categories} {--products-only : Seed only products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed categories and products data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categoriesOnly = $this->option('categories-only');
        $productsOnly = $this->option('products-only');

        if ($categoriesOnly) {
            $this->info('Seeding categories...');
            $this->call(CategorySeeder::class);
            $this->info('Categories seeded successfully!');
            return;
        }

        if ($productsOnly) {
            $this->info('Seeding products...');
            $this->call(ProductSeeder::class);
            $this->info('Products seeded successfully!');
            return;
        }

        $this->info('Seeding categories and products...');
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->info('Categories and products seeded successfully!');
    }
}
