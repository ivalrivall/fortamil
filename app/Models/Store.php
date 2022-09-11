<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['deleted_at','user_id','marketplace_id', 'created_at', 'updated_at'];

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

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
