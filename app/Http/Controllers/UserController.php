<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\UserItem;

class UserController extends Controller
{

public function createUser(Request $request){
//dd($request->name);

return User::create(['full_name' => $request->full_name,
    'first_name' => $request->first_name,
    'last_name' => $request->last_name,
    'username' => $request->username,
    'email' => $request->email,
    'password' => $request->password ]);

}
public function getAll( ){

    // Facade
    return DB::table('users')
        ->whereIn('users.id', [1,2])
        ->leftJoin('items', 'users.id', '=', 'items.user_id')
        ->get();

    // Eloquent
    return User::with(['items'])->get();
}
public function getMoney( ){


    }
public function updateUser(Request $request){
    //dd($request->name);

    // $user = User::find(1);
    // $user -> name = "joyG";
    // $user -> email = "jys@gmail.com";
    // $user -> password = "12345";

    // return $user;
     User::where('email', $request->email)->update(['name'=> $request->name]);

    }
    // TODO DELETE BASED ON ID
public function deleteUser(Request $request){
        // dd('test');
        //return User::where('id', $request->id)->delete(); // improve by having try catch
        try {
            // Check if the user exists
            $user = User::find($request->id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $user->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the user'], 500);
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
            return response()->json(['message' => 'Invalid username or password'], 401);
        }

        // Generate Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logoutUser(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    public function buyItem(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'quantity' => 'required|integer|min:1', // Ensure quantity is required
            ]);

            $user = auth()->user(); // Ensure user is authenticated

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $item = Item::findOrFail($request->item_id);

            // Check if the user has enough money
            $totalCost = $item->price * $request->quantity;
            if ($user->money < $totalCost) {
                return response()->json(['error' => 'Not enough money'], 400);
            }

            // Deduct money from user
            $user->money -= $totalCost;
            $user->save();

            // Add or update the item in the user's inventory
            $userItem = UserItem::where('user_id', $user->id)
                ->where('item_id', $item->id)
                ->first();

            if ($userItem) {
                $userItem->quantity += $request->quantity;
                $userItem->save();
            } else {
                UserItem::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'quantity' => $request->quantity,
                ]);
            }

            return response()->json(['message' => 'Item purchased successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



}
