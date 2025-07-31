<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
    ];

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk filter berdasarkan tipe
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope untuk filter berdasarkan status
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Method untuk mendapatkan label tipe transaksi
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjustment' => 'Penyesuaian',
            default => $this->type,
        };
    }

    // Method untuk mendapatkan label status
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Tertunda',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    // Method untuk mendapatkan warna status
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    // Method untuk mendapatkan warna tipe
    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'in' => 'green',
            'out' => 'red',
            'adjustment' => 'blue',
            default => 'gray',
        };
    }
}
