<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * slug: admin, warehouse_officer, dropshipper, super_admin, cashier
 */
class Role extends Model
{
    use HasFactory, SoftDeletes;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
