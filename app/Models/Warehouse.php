<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'address', 'picture', 'created_by'];

    protected $hidden = ['deleted_at','created_by','created_at'];

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

    public function whitelists()
    {
        return $this->belongsToMany(User::class, 'warehouse_whitelists', 'warehouse_id', 'user_id');
    }
}
