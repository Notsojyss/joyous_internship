<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    public function getActive(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $listings = DB::table('market_listings')
            ->join('items', 'market_listings.item_id', '=', 'items.id')
            ->join('users', 'market_listings.user_id', '=', 'users.id')
            ->where('market_listings.status', 'active')
            ->select(
                'market_listings.*',
                'items.item_name',
                'items.image',
                'items.rarity',
                'items.description',
                'users.username'
            )
            ->get();

        return response()->json($listings);
    }


    public function sellItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user(); // Use Auth facade
        $itemId = $request->item_id;
        $quantity = $request->quantity;
        $price = $request->price;

        // Check if user has enough quantity of the item
        $userItem = DB::table('user_items')
            ->where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();

        if (!$userItem || $userItem->quantity < $quantity) {
            return response()->json(['message' => 'Not enough items to sell'], 400);
        }

        // Deduct from user's inventory
        if ($userItem->quantity == $quantity) {
            DB::table('user_items')->where('id', $userItem->id)->delete();
        } else {
            DB::table('user_items')->where('id', $userItem->id)->decrement('quantity', $quantity);
        }

        // Add item to market listings
        DB::table('market_listings')->insert([
            'user_id' => $user->id,
            'item_id' => $itemId,
            'quantity' => $quantity,
            'price' => $price,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Item listed on the market'], 200);
    }


}
