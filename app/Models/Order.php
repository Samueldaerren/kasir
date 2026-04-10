<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'customer_id',
        'sale_date',
        'total_price',
        'total_pay',
        'total_return',
        'point_earned',
        'point_used',
    ];

    /**
     * Get the user (employee) who created this order
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the customer who made this order
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all detail orders for this order
     */
    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class);
    }

    public function getTotalAttribute()
    {
        return $this->attributes['total_price'] ?? null;
    }

    public function getTotalPriceRupiahAttribute()
    {
        return $this->formatRupiah($this->total_price);
    }

    public function getTotalPayRupiahAttribute()
    {
        return $this->formatRupiah($this->total_pay);
    }

    public function getTotalReturnRupiahAttribute()
    {
        return $this->formatRupiah($this->total_return);
    }

    public function getTanggalOrderAttribute()
    {
        return $this->attributes['sale_date'] ?? null;
    }

    protected function formatRupiah($value)
    {
        if ($value === null) {
            return null;
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
