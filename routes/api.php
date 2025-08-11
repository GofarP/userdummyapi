<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['jwt.auth', 'jwt.cookie'])->group(function () {
    Route::get('/user', [UserController::class, 'dataWithPagination']);
    Route::apiResource('users', UserController::class);
    Route::get('/searchuser', [UserController::class, 'searchUser']);
    Route::get('/me',[AuthController::class,'getCurrentUser']);
    Route::post('/refresh',[AuthController::class,'refresh']);

});
