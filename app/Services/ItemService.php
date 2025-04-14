<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemService
{
    public function getShopitems()
    {
        return Item::get();
    }
}
