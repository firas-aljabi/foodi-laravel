<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    
    protected $fillable=[
        'id','name','image','price_by_piece','branch_id'
        ];

        public function products(){

            return $this->belongsToMany(Product::class,'products_ingredients');

        }
        public function orders()
        {
        return $this->belongsToMany(Order::class,'orders_ingredients');
        }

        public function branch(){
            return $this->belongsTo(Branch::class);
        }
}
