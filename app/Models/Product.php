<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'co2_risparmiata',
    ];

    /**
     * Get the orders that belong to the product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_products');
    }
}
