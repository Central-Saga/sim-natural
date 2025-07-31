# Categories dan Products - Model, Factory, dan Seeder

## Overview

Sistem ini telah dilengkapi dengan model, factory, dan seeder untuk mengelola kategori dan produk.

## Models

### Category Model (`app/Models/Category.php`)

-   **Fillable fields**: `name`, `description`, `status`
-   **Relationships**:
    -   `products()` - HasMany relationship dengan Product
-   **Scopes**:
    -   `active()` - Filter kategori yang aktif

### Product Model (`app/Models/Product.php`)

-   **Fillable fields**: `name`, `foto_produk`, `kategori_id`, `status`, `stock_quantity`
-   **Relationships**:
    -   `category()` - BelongsTo relationship dengan Category
-   **Scopes**:
    -   `active()` - Filter produk yang aktif
    -   `inStock()` - Filter produk yang memiliki stok

## Factories

### CategoryFactory (`database/factories/CategoryFactory.php`)

-   Menghasilkan data kategori dengan nama dan deskripsi yang realistis
-   **States**:
    -   `active()` - Kategori dengan status aktif
    -   `inactive()` - Kategori dengan status tidak aktif

### ProductFactory (`database/factories/ProductFactory.php`)

-   Menghasilkan data produk dengan gambar dummy dan stok acak
-   **States**:
    -   `active()` - Produk dengan status aktif
    -   `inactive()` - Produk dengan status tidak aktif
    -   `inStock()` - Produk dengan stok tersedia
    -   `outOfStock()` - Produk tanpa stok

## Seeders

### CategorySeeder (`database/seeders/CategorySeeder.php`)

-   Mengisi 10 kategori default:
    1. Elektronik
    2. Fashion
    3. Makanan & Minuman
    4. Kesehatan & Kecantikan
    5. Rumah Tangga
    6. Olahraga
    7. Buku & Alat Tulis
    8. Otomotif
    9. Mainan & Hobi
    10. Pertanian

### ProductSeeder (`database/seeders/ProductSeeder.php`)

-   Mengisi 15 produk contoh dengan kategori yang sesuai
-   Setiap kategori memiliki beberapa produk contoh

## Database Seeder

Seeder telah ditambahkan ke `DatabaseSeeder.php` dan akan dijalankan secara otomatis saat `php artisan db:seed`.

## Commands

### Custom Command

`php artisan seed:categories-products`

-   `--categories-only` - Seed hanya kategori
-   `--products-only` - Seed hanya produk

## Usage Examples

### Menggunakan Factory

```php
// Membuat kategori
$category = Category::factory()->create();

// Membuat produk dengan kategori
$product = Product::factory()->for($category)->create();

// Membuat produk aktif dengan stok
$product = Product::factory()->active()->inStock()->create();
```

### Menggunakan Scopes

```php
// Ambil kategori aktif
$activeCategories = Category::active()->get();

// Ambil produk aktif dengan stok
$availableProducts = Product::active()->inStock()->get();

// Ambil produk berdasarkan kategori
$electronics = Category::where('name', 'Elektronik')->first();
$electronicsProducts = $electronics->products;
```

### Menjalankan Seeder

```bash
# Seed semua data
php artisan db:seed

# Seed hanya categories dan products
php artisan seed:categories-products

# Seed hanya categories
php artisan seed:categories-products --categories-only

# Seed hanya products
php artisan seed:categories-products --products-only
```

## Data Structure

### Categories Table

-   `id` - Primary key
-   `name` - Nama kategori
-   `description` - Deskripsi kategori
-   `status` - Status (active/inactive)
-   `created_at`, `updated_at` - Timestamps

### Products Table

-   `id` - Primary key
-   `name` - Nama produk
-   `foto_produk` - URL foto produk
-   `kategori_id` - Foreign key ke categories
-   `status` - Status (active/inactive)
-   `stock_quantity` - Jumlah stok
-   `created_at`, `updated_at` - Timestamps
