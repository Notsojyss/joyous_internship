<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\PvpController;


Route::post('/user/create', [UserController::class, 'createUser']);
Route::post('/user/update', [UserController::class, 'updateUser']);
Route::delete('/user/delete', [UserController::class, 'deleteUser']);
Route::get('/users/get-all', [UserController::class, 'getAll']);
Route::post('/user/login', [UserController::class, 'loginUser']);
Route::middleware('auth:sanctum')->post('/user/logout', [UserController::class, 'logoutUser']);
Route::get('/items/get-all', [ItemController::class, 'getAllitems']);
Route::get('/item/get-shopitems', [ItemController::class, 'getShopitems']);
Route::middleware(['auth:sanctum'])->post('/buy-item', [MarketController::class, 'buyItem']);
Route::middleware('auth:sanctum')->get('/user/getMoney', [UserController::class, 'getMoney']);
Route::middleware('auth:sanctum')->get('/user/getUsersitem', [UserController::class, 'getUserItems']);
Route::middleware('auth:sanctum')->post('/market/sellitem', [MarketController::class, 'sellItem']);
Route::middleware('auth:sanctum')->post('/market/cancelsellitem', [MarketController::class, 'cancelListing']);
Route::middleware('auth:sanctum')->get('/market/get-activelisting', [MarketController::class, 'getActive']);
Route::middleware('auth:sanctum')->get('/market/get-useritemforsale', [MarketController::class, 'getUserItemForSale']);
Route::middleware('auth:sanctum')->get('/market/get-itemhistory', [MarketController::class, 'getItemHistory']);
Route::middleware('auth:sanctum')->post('/pvp/assignPlay', [PvpController::class, 'assignPlay']);
Route::middleware('auth:sanctum')->get('/pvp/get-pvpbattles', [PvpController::class, 'getPvpBattles']);
Route::middleware('auth:sanctum')->post('/pvp/join-battle/{pvpId}', [PvpController::class, 'joinBattle']);
