<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\UserItem;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createUser(Request $request){
        return $this->userService->createUser($request);
    }

    /**
     * Get all user
     * @return void
     */
    public function getAll( ){

        User::get();

    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserItems(Request $request) {
            return $this->userService->getUserItems();
    }

    /**Get the money value of the user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getMoney(Request $request){
    return $this->userService->getMoney($request);
    }

    /**Login with username and password and giving it a token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginUser(Request $request)
    {
        return $this->userService->loginUser($request);
    }

    /**
     * When user log out their token is deleted
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutUser(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * For updating user's data; this use the updateUser from UserService
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser (Request $request){
         return $this->userService->updateUser($request);
    }

    /**
     * Fetching the user; uses the fetchUser from User Service
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchUser(Request $request){
        return $this->userService->fetchUser($request);
    }

}
