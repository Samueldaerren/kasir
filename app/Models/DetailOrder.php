<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
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
        return $this->belongsTo(Produk::class, 'produk_id');
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
    }}
