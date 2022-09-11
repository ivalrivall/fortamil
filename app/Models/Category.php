<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
