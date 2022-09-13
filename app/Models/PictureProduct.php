<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PictureProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'picture_products';
    protected $guarded = [];
    protected $hidden = ['product_id', 'deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
