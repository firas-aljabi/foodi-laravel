<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderProduct extends Pivot
{
    protected $table = 'orders_products';
    protected $fillable = ['product_id', 'order_id', 'quantity'];
    
    public function product()
    {
        return $this->belongsToMany(Product::class);
    }
   
    public function orders(){
        return $this->belongsToMany(Order::class);
    }
}
