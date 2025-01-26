<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post("login" , [AuthController::class , "login"]);
Route::post("register", [AuthController::class, "register"]);

Route::group(["middleware"=>["auth:sanctum"]] , function () {
    Route::post("logout", [AuthController::class, "logout"]);

    Route::get("profile" , [UserController::class , "index"]);;
    Route::put("profile" , [UserController::class , "update"]);
    Route::put("profile/password", [UserController::class, "updatePassword"]);

});

