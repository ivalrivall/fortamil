<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $table = 'indonesia_villages';

    protected $casts = ['meta' => 'json'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
