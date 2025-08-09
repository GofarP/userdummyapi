<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [UserController::class, 'dataWithPagination']);

Route::middleware(['jwt.auth','jwt.cookie', 'auth:api'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('/searchuser', [UserController::class, 'searchUser']);
});
