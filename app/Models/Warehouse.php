<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'address', 'picture', 'created_by'];

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'warehouse_id');
    }

    /**
     * Get most recent address.
     */
    public function latestAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->latestOfMany();
    }
}
