<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'order_id',
        'amount',
        'sub_total',
    ];

    /**
     * Get the order this detail belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this detail
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'product_id');
    }
    public function getJumlahAttribute()
    {
        return $this->attributes['amount'] ?? null;
    }

    public function getHargaAttribute()
    {
        $amount = $this->attributes['amount'] ?? 0;
        if ($amount > 0) {
            return ($this->attributes['sub_total'] ?? 0) / $amount;
        }
        return null;
    }

    public function getHargaRupiahAttribute()
    {
        return $this->formatRupiah($this->harga);
    }

    public function getSubTotalRupiahAttribute()
    {
        return $this->formatRupiah($this->attributes['sub_total'] ?? null);
    }

    protected function formatRupiah($value)
    {
        if ($value === null) {
            return null;
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
