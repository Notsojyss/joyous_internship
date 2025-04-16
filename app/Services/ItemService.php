<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemService
{
    /**
     * Get the items in the items table, this is for viewing of the items in official shop
     * @return mixed
     */
    public function getShopitems()
    {
        return Item::get();
    }
}
