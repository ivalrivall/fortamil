<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products', 'product_id', 'order_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function pictures()
    {
        return $this->hasMany(PictureProduct::class, 'product_id');
    }
}
