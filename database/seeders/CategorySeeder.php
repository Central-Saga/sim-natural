<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bahan Baku Kertas',
                'description' => 'Bahan baku kertas seperti kertas coklat, kertas karton, dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Bahan Baku Tekstil',
                'description' => 'Bahan baku tekstil seperti kain kaca dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Bahan Baku Kayu',
                'description' => 'Bahan baku kayu seperti plat kayu dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Bahan Baku Bambu',
                'description' => 'Bahan baku bambu seperti sisitan bambu dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Bahan Baku Perekat',
                'description' => 'Bahan baku perekat seperti lem dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Produk Jadi Pot',
                'description' => 'Produk jadi berupa pot seperti Pot lontar, Pot karung, dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Produk Jadi Kerajinan',
                'description' => 'Produk jadi kerajinan seperti Amy, AH, Bh banana, dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Produk Jadi Dapur',
                'description' => 'Produk jadi untuk dapur seperti KDB, KDK, Kukusan, dan sejenisnya',
                'status' => 'active',
            ],
            [
                'name' => 'Bahan Baku Lainnya',
                'description' => 'Bahan baku lainnya yang belum dikategorikan',
                'status' => 'active',
            ],
            [
                'name' => 'Produk Jadi Lainnya',
                'description' => 'Produk jadi lainnya yang belum dikategorikan',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
