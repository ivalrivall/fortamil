<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'indonesia_districts';

    protected $casts = ['meta' => 'json'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
