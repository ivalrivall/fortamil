<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * status: new, on-progress, cancel, done, return
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id');
    }

    public function trackings()
    {
        return $this->hasMany(Tracking::class);
    }
}
