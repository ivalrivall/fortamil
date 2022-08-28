<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
