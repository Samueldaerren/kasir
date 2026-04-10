<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
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

    public function getTanggalOrderAttribute()
    {
        return $this->attributes['sale_date'] ?? null;
    }
}
