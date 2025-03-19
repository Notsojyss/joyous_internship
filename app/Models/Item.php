<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'description',
        'rarity',
        'price',
        'image'

    ];


}
