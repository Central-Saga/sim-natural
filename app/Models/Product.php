<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'foto_produk',
        'kategori_id',
        'status',
        'stock_quantity',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include products with stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to only include products out of stock.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', 0);
    }

    /**
     * Get the image URL for the product.
     */
    public function getImageUrlAttribute()
    {
        if (!$this->foto_produk) {
            return null;
        }

        if (filter_var($this->foto_produk, FILTER_VALIDATE_URL)) {
            return $this->foto_produk;
        }

        return \Storage::url($this->foto_produk);
    }

    /**
     * Check if the image is from external URL.
     */
    public function isExternalImage()
    {
        return $this->foto_produk && filter_var($this->foto_produk, FILTER_VALIDATE_URL);
    }

    /**
     * Check if the image is from storage.
     */
    public function isStorageImage()
    {
        return $this->foto_produk && !filter_var($this->foto_produk, FILTER_VALIDATE_URL);
    }
}
