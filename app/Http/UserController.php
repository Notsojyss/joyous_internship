<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
   
public function createUser(Request $request){
//dd($request->name);

return User::create(['name' => $request->name, 'email' => $request->email, 'password' => $request->password ]); 

}
public function getAll(Request $request){
    // dd('test');
    return User::get();//->take($request->count);
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



}
