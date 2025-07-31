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
            // Bahan Baku Kertas (kategori_id: 1)
            [
                'name' => 'Kertas Coklat Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=1',
                'kategori_id' => 1,
                'status' => 'active',
                'stock_quantity' => 500,
            ],
            [
                'name' => 'Kertas Karton Kraft',
                'foto_produk' => 'https://picsum.photos/640/480?random=2',
                'kategori_id' => 1,
                'status' => 'active',
                'stock_quantity' => 300,
            ],
            [
                'name' => 'Kertas Karton Duplex',
                'foto_produk' => 'https://picsum.photos/640/480?random=3',
                'kategori_id' => 1,
                'status' => 'active',
                'stock_quantity' => 250,
            ],

            // Bahan Baku Tekstil (kategori_id: 2)
            [
                'name' => 'Kain Kaca Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=4',
                'kategori_id' => 2,
                'status' => 'active',
                'stock_quantity' => 200,
            ],
            [
                'name' => 'Kain Kaca Standard',
                'foto_produk' => 'https://picsum.photos/640/480?random=5',
                'kategori_id' => 2,
                'status' => 'active',
                'stock_quantity' => 350,
            ],

            // Bahan Baku Kayu (kategori_id: 3)
            [
                'name' => 'Plat Kayu Jati',
                'foto_produk' => 'https://picsum.photos/640/480?random=6',
                'kategori_id' => 3,
                'status' => 'active',
                'stock_quantity' => 100,
            ],
            [
                'name' => 'Plat Kayu Mahoni',
                'foto_produk' => 'https://picsum.photos/640/480?random=7',
                'kategori_id' => 3,
                'status' => 'active',
                'stock_quantity' => 150,
            ],

            // Bahan Baku Bambu (kategori_id: 4)
            [
                'name' => 'Sisitan Bambu Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=8',
                'kategori_id' => 4,
                'status' => 'active',
                'stock_quantity' => 400,
            ],
            [
                'name' => 'Sisitan Bambu Standard',
                'foto_produk' => 'https://picsum.photos/640/480?random=9',
                'kategori_id' => 4,
                'status' => 'active',
                'stock_quantity' => 600,
            ],

            // Bahan Baku Perekat (kategori_id: 5)
            [
                'name' => 'Lem Kayu Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=10',
                'kategori_id' => 5,
                'status' => 'active',
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Lem Kertas',
                'foto_produk' => 'https://picsum.photos/640/480?random=11',
                'kategori_id' => 5,
                'status' => 'active',
                'stock_quantity' => 75,
            ],

            // Produk Jadi Pot (kategori_id: 6)
            [
                'name' => 'Pot Lontar Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=12',
                'kategori_id' => 6,
                'status' => 'active',
                'stock_quantity' => 200,
            ],
            [
                'name' => 'Pot Karung Standard',
                'foto_produk' => 'https://picsum.photos/640/480?random=13',
                'kategori_id' => 6,
                'status' => 'active',
                'stock_quantity' => 300,
            ],

            // Produk Jadi Kerajinan (kategori_id: 7)
            [
                'name' => 'Amy - Kerajinan Bambu',
                'foto_produk' => 'https://picsum.photos/640/480?random=14',
                'kategori_id' => 7,
                'status' => 'active',
                'stock_quantity' => 150,
            ],
            [
                'name' => 'AH - Kerajinan Kayu',
                'foto_produk' => 'https://picsum.photos/640/480?random=15',
                'kategori_id' => 7,
                'status' => 'active',
                'stock_quantity' => 100,
            ],
            [
                'name' => 'Bh Banana - Kerajinan Pisang',
                'foto_produk' => 'https://picsum.photos/640/480?random=16',
                'kategori_id' => 7,
                'status' => 'active',
                'stock_quantity' => 250,
            ],

            // Produk Jadi Dapur (kategori_id: 8)
            [
                'name' => 'KDB - Keranjang Dapur Bambu',
                'foto_produk' => 'https://picsum.photos/640/480?random=17',
                'kategori_id' => 8,
                'status' => 'active',
                'stock_quantity' => 180,
            ],
            [
                'name' => 'KDK - Keranjang Dapur Kayu',
                'foto_produk' => 'https://picsum.photos/640/480?random=18',
                'kategori_id' => 8,
                'status' => 'active',
                'stock_quantity' => 120,
            ],
            [
                'name' => 'Kukusan Bambu Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=19',
                'kategori_id' => 8,
                'status' => 'active',
                'stock_quantity' => 80,
            ],

            // Bahan Baku Lainnya (kategori_id: 9)
            [
                'name' => 'Tali Rafia Premium',
                'foto_produk' => 'https://picsum.photos/640/480?random=20',
                'kategori_id' => 9,
                'status' => 'active',
                'stock_quantity' => 1000,
            ],
            [
                'name' => 'Paku Kayu',
                'foto_produk' => 'https://picsum.photos/640/480?random=21',
                'kategori_id' => 9,
                'status' => 'active',
                'stock_quantity' => 500,
            ],

            // Produk Jadi Lainnya (kategori_id: 10)
            [
                'name' => 'Tas Anyaman Bambu',
                'foto_produk' => 'https://picsum.photos/640/480?random=22',
                'kategori_id' => 10,
                'status' => 'active',
                'stock_quantity' => 75,
            ],
            [
                'name' => 'Topi Bambu',
                'foto_produk' => 'https://picsum.photos/640/480?random=23',
                'kategori_id' => 10,
                'status' => 'active',
                'stock_quantity' => 200,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
