<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\UserItem;
use Illuminate\Support\Facades\Validator;

class UserService
{
    /**
     * For creation of User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request)
    {
        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user using mass assignment
        $user = User::create([
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Return a success response with the created user
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Updating User's data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
        public function updateUser(Request $request){
            $validated = $request->validate([
                'username' => 'required|unique:users,username,'. Auth::id(),
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
                'email'      => 'required|email|unique:users,email,' . Auth::id(),
            ]);
            $validated['full_name'] = $request->first_name . " " . $request->last_name;
            $validated['updated_at'] = now();
            $user = Auth::user();
            $user->update($validated);

            return response()->json(['message' => 'Profile updated successfully.', 'user' => $user], 201);

        }

    /**
     * Fetching the user's data
     * @return \Illuminate\Http\JsonResponse
     */
        public function fetchUser(){
            $user = Auth::user();
            return response()->json($user);
        }

    /**
     * Get the items of the users
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserItems()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $items = UserItem::with('item')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($items);

//        $userId = auth()->id(); // Get the logged-in user's ID
//
//        $items = DB::table('users')
//            ->leftJoin('user_items', 'users.id', '=', 'user_items.user_id')
//            ->leftJoin('items', 'user_items.item_id', '=', 'items.id')
//            ->select(
//                'users.id as user_id',
//                'users.full_name',
//                'users.first_name',
//                'users.last_name',
//                'users.email',
//                'users.username',
//                'items.item_name',
//                'items.description',
//                'items.rarity',
//                'items.image',
//                'user_items.item_id as item_id',
//                'user_items.quantity',
//            )
//            ->where('users.id', $userId)
//            ->get()
//            ->groupBy('user_id')
//            ->map(function ($items) {
//                $user = $items->first();
//
//                return [
//                    'user_id' => $user->user_id,
//                    'full_name' => $user->full_name,
//                    'first_name' => $user->first_name,
//                    'last_name' => $user->last_name,
//                    'username' => $user->username,
//                    'email' => $user->email,
//                    'items' => $items->map(function ($item) {
//                        return [
//                            'item_name' => $item->item_name,
//                            'item_id' => $item->item_id,
//                            'description' => $item->description,
//                            'rarity' => $item->rarity,
//                            'image' => $item->image,
//                            'quantity' => $item->quantity
//                        ];
//                    })->toArray()
//                ];
//            })
//            ->values(); // Convert collection to array
//
//        return response()->json($items);
    }

    /**
     * get the value of money user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * login user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginUser(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Find user by username
        $user = User::withTrashed()->where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalids username or password'], 401);
        }
        if ($user->trashed()) {
            return response()->json(['message' => 'Account has been deleted.'], 403);
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
