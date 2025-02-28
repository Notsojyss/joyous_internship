<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/user/create', [UserController::class, 'createUser']);
Route::post('/user/update', [UserController::class, 'updateUser']);
Route::delete('/user/delete', [UserController::class, 'deleteUser']);
Route::get('/users/get-all', [UserController::class, 'getAll']);