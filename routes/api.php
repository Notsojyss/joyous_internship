<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;

Route::post('/user/create', [UserController::class, 'createUser']);
Route::post('/user/update', [UserController::class, 'updateUser']);
Route::delete('/user/delete', [UserController::class, 'deleteUser']);
Route::get('/users/get-all', [UserController::class, 'getAll']);
Route::post('/user/login', [UserController::class, 'loginUser']);
Route::middleware('auth:sanctum')->post('/user/logout', [UserController::class, 'logoutUser']);
Route::get('/items/get-all', [ItemController::class, 'getAllitems']);
Route::get('/item/get-shopitems', [ItemController::class, 'getShopitems']);
