<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserItem extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
    ];

    // Relationship: Each user item belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
