<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'total_poin',
    ];

    /**
     * Get all orders for this customer
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getNamaCustomerAttribute()
    {
        return $this->attributes['name'] ?? null;
    }
}
