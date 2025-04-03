<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\alert;
use function Laravel\Prompts\error;

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
                'items.id as item_id',
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

    public function getUserItemForSale(Request $request)
    {
        $userId = auth()->id(); // Get the logged-in user's ID

        $items = DB::table('users')
            ->leftJoin('market_listings', 'users.id', '=', 'market_listings.user_id')
            ->leftJoin('items', 'market_listings.item_id', '=', 'items.id')
            ->select([
                'users.id as user_id',
                'users.username',
                'items.item_name',
                'items.description',
                'items.rarity',
                'items.image',
                'market_listings.id as listing_id',
                'market_listings.quantity',
                'market_listings.price',
                'market_listings.status'
            ])
            ->where('market_listings.status', 'active')
            ->where('users.id', $userId)
            ->get();

        return response()->json($items);
    }

    public function cancelListing(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:market_listings,id',
        ]);

        $user = Auth::user();
        $listing = DB::table('market_listings')
            ->where('id', $request->listing_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        DB::table('user_items')->updateOrInsert(
            ['user_id' => $user->id, 'item_id' => $listing->item_id],
            ['quantity' => DB::raw("quantity + $listing->quantity"),
                'updated_at' => now(),
                'created_at' => DB::raw('IFNULL(created_at, NOW())')]
        );

        // Remove listing from market
        DB::table('market_listings')->where('id', $listing->id)->delete();

        return response()->json(['message' => 'Listing canceled and item returned'], 200);
    }

    public function buyItem(Request $request)
    {
        try {
            $request->validate([
                'listing_id' => 'required_if:from_market,true|integer|exists:market_listings,id',
                'item_id' => 'required_if:from_market,false|integer|exists:items,id',
                'quantity' => 'required|integer|min:1',
                'from_market' => 'required|boolean'
            ]);


            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $item = null;
            $totalCost = 0;
            $seller = null;

            DB::beginTransaction();

            if ($request->from_market) {
                // Fetch listing from market
                $listing = DB::table('market_listings')->where('id', $request->listing_id)->first();

                if (!$listing) {
                    return response()->json(['error' => 'Listing not found'], 404);
                }
                if ($listing->user_id == $user->id) {
                    return response()->json(['error' => 'You cannot buy your own item'], 400);
                }

                if ($listing->quantity < $request->quantity) {
                    return response()->json(['error' => 'Not enough items available'], 400);
                }

                $item = DB::table('items')->where('id', $listing->item_id)->first();
                $totalCost = $listing->price * $request->quantity;
                $seller = $listing->user_id;

            } else {
                // Fetch item from shop
                $item = DB::table('items')->where('id', $request->item_id)->first();

                if (!$item) {
                    return response()->json(['error' => 'Item not found'], 404);
                }

                $totalCost = $item->price * $request->quantity;
            }

            // Check if buyer has enough money
            if ($user->money < $totalCost) {
                return response()->json(['error' => 'Not enough money'], 400);
            }

            // Deduct money from buyer
            DB::table('users')->where('id', $user->id)->decrement('money', $totalCost);

            if ($request->from_market) {
                // Transfer money to the seller
                DB::table('users')->where('id', $seller)->increment('money', $totalCost);
                    DB::table('market_listings')
                        ->where('id', $listing->id)
                        ->update(['status' => 'sold', 'updated_at' => now()]);
            }

            // Check if buyer already owns the item
            $userItem = DB::table('user_items')
                ->where('user_id', $user->id)
                ->where('item_id', $item->id)
                ->first();

            if ($userItem) {
                // Increase quantity for already owned item
                DB::table('user_items')
                    ->where('user_id', $user->id)
                    ->where('item_id', $item->id)
                    ->increment('quantity', $request->quantity);
            } else {
                // Insert new item into inventory
                DB::table('user_items')->insert([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'quantity' => $request->quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Item purchased successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getItemHistory(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        $itemhistory = DB::table('market_listings')
            ->join('items', 'market_listings.item_id', '=', 'items.id')
            ->join('users', 'market_listings.user_id', '=', 'users.id')
            ->where('market_listings.status', 'sold')
            ->where('market_listings.item_id', $request->item_id)
            ->select(
                'market_listings.id',
                'items.item_name',
                'market_listings.price as price per item',
                'market_listings.quantity as quantity',
                'market_listings.updated_at as market_updated_at',
                'users.username',
            )
            ->orderByDesc('market_listings.updated_at')
            ->get();

        return response()->json($itemhistory);
    }
}


