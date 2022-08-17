<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get most recent address.
     */
    public function latestAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->latestOfMany();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
