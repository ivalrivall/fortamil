<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @param priority: high, medium, low
 */
class Notification extends Model
{
    use HasFactory;

    protected $hidden = ['deleted_at','created_at','user_id'];

    protected $guarded = [];

    protected $casts = [
        'data' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
