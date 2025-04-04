<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        // aggiungi i campi necessari per l'ordine
    ];

    /**
     * Get the products that belong to the order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_products');
    }
}
