<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Elektronik
            [
                'name' => 'Smartphone Samsung Galaxy A54',
                'foto_produk' => 'https://picsum.photos/640/480?random=1',
                'kategori_id' => 1,
                'status' => 'active',
                'stock_quantity' => 25,
            ],
            [
                'name' => 'Laptop ASUS VivoBook',
                'foto_produk' => 'https://picsum.photos/640/480?random=2',
                'kategori_id' => 1,
                'status' => 'active',
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Headphone Sony WH-1000XM4',
                'foto_produk' => 'https://picsum.photos/640/480?random=3',
                'kategori_id' => 1,
                'status' => 'active',
                'stock_quantity' => 30,
            ],

            // Fashion
            [
                'name' => 'Kemeja Pria Casual',
                'foto_produk' => 'https://picsum.photos/640/480?random=4',
                'kategori_id' => 2,
                'status' => 'active',
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Dress Wanita Elegant',
                'foto_produk' => 'https://picsum.photos/640/480?random=5',
                'kategori_id' => 2,
                'status' => 'active',
                'stock_quantity' => 35,
            ],
            [
                'name' => 'Sepatu Sneakers Nike',
                'foto_produk' => 'https://picsum.photos/640/480?random=6',
                'kategori_id' => 2,
                'status' => 'active',
                'stock_quantity' => 20,
            ],

            // Makanan & Minuman
            [
                'name' => 'Kopi Arabika Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=7',
                'kategori_id' => 3,
                'status' => 'active',
                'stock_quantity' => 100,
            ],
            [
                'name' => 'Snack Keripik Kentang',
                'foto_produk' => 'https://picsum.photos/640/480?random=8',
                'kategori_id' => 3,
                'status' => 'active',
                'stock_quantity' => 75,
            ],
            [
                'name' => 'Teh Hijau Organik',
                'foto_produk' => 'https://picsum.photos/640/480?random=9',
                'kategori_id' => 3,
                'status' => 'active',
                'stock_quantity' => 60,
            ],

            // Kesehatan & Kecantikan
            [
                'name' => 'Vitamin C 1000mg',
                'foto_produk' => 'https://picsum.photos/640/480?random=10',
                'kategori_id' => 4,
                'status' => 'active',
                'stock_quantity' => 80,
            ],
            [
                'name' => 'Skincare Face Wash',
                'foto_produk' => 'https://picsum.photos/640/480?random=11',
                'kategori_id' => 4,
                'status' => 'active',
                'stock_quantity' => 45,
            ],
            [
                'name' => 'Minyak Essential Lavender',
                'foto_produk' => 'https://picsum.photos/640/480?random=12',
                'kategori_id' => 4,
                'status' => 'active',
                'stock_quantity' => 30,
            ],

            // Rumah Tangga
            [
                'name' => 'Blender Philips',
                'foto_produk' => 'https://picsum.photos/640/480?random=13',
                'kategori_id' => 5,
                'status' => 'active',
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Panci Stainless Steel',
                'foto_produk' => 'https://picsum.photos/640/480?random=14',
                'kategori_id' => 5,
                'status' => 'active',
                'stock_quantity' => 25,
            ],
            [
                'name' => 'Vacuum Cleaner',
                'foto_produk' => 'https://picsum.photos/640/480?random=15',
                'kategori_id' => 5,
                'status' => 'active',
                'stock_quantity' => 10,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
