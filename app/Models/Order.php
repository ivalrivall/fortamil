<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @param status: waiting, rejected (di tolak admin), accepted (di terima admin), packing (kalau udah scan warehouse),
 *  delivered, arrived, cancel, complaint, return
 * @param optional_status: success, failed
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['deleted_at'];

    protected $hidden = ['deleted_at', 'user_id', 'store_id', 'customer_id', 'warehouse_id'];

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

    public function note()
    {
        return $this->morphOne(Note::class, 'notable');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
