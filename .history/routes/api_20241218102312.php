<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::apiResource('users', UserController::class);
Route::get('/user', [UserController::class, 'dataWithPagination']);

Route::get('/user', [UserController::class, 'searchUser']);
