<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Notification extends Model
{
    use HasFactory;

    protected $hidden = ['deleted_at','created_at','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
