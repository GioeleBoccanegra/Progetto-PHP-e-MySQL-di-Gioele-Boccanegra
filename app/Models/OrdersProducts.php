<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersProducts extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantita'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
