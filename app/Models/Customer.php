<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['user_id', 'deleted_at', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user's most recent address.
     */
    public function latestAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->latestOfMany();
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
