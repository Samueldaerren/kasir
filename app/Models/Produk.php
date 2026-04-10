<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produk extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'price',
        'stock',
        'image',
    ];

    /**
     * Get all detail orders for this product
     */
    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class, 'produk_id');
    }

    public function getNamaProdukAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    public function getHargaAttribute()
    {
        return $this->attributes['price'] ?? null;
    }

    public function getHargaRupiahAttribute()
    {
        return $this->formatRupiah($this->harga);
    }

    protected function formatRupiah($value)
    {
        if ($value === null) {
            return null;
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    public function getStokAttribute()
    {
        return $this->attributes['stock'] ?? null;
    }

    public function getImageUrlAttribute()
    {
        $image = $this->attributes['image'] ?? null;
        if (! $image) {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        if (Str::startsWith($image, '/')) {
            return asset($image);
        }

        return asset('storage/' . ltrim($image, '/'));
    }
}
