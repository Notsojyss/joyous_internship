<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(Request $request){
            return User::create(['full_name' => $request->full_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password ]);

        }
    public function getUserItems()
    {
        $userId = auth()->id(); // Get the logged-in user's ID

        $items = DB::table('users')
            ->leftJoin('user_items', 'users.id', '=', 'user_items.user_id')
            ->leftJoin('items', 'user_items.item_id', '=', 'items.id')
            ->select(
                'users.id as user_id',
                'users.full_name',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.username',
                'items.item_name',
                'items.description',
                'items.rarity',
                'items.image',
                'user_items.item_id as item_id',
                'user_items.quantity',
            )
            ->where('users.id', $userId)
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                $user = $items->first();

                return [
                    'user_id' => $user->user_id,
                    'full_name' => $user->full_name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'items' => $items->map(function ($item) {
                        return [
                            'item_name' => $item->item_name,
                            'item_id' => $item->item_id,
                            'description' => $item->description,
                            'rarity' => $item->rarity,
                            'image' => $item->image,
                            'quantity' => $item->quantity
                        ];
                    })->toArray()
                ];
            })
            ->values(); // Convert collection to array

        return response()->json($items);
    }

    public function getMoney(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return response()->json(['money' => $user->money], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function loginUser(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Find user by username
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalids username or password'], 401);
        }
        // Generate Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logins successful',
            'token' => $token,
            'user' => $user
        ]);
    }
}
