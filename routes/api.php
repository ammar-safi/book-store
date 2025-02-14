<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post("login", [AuthController::class, "login"]);
Route::post("register", [AuthController::class, "register"]);

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::post("logout", [AuthController::class, "logout"]);

    Route::get("profile", [UserController::class, "profile"]);;
    Route::put("profile", [UserController::class, "update"]);
    Route::put("profile/password", [UserController::class, "updatePassword"]);

    Route::group(["prefix" => "books"], function () {
        Route::get("/", [UserController::class, "index"]);
        Route::post("/", [UserController::class, "store"]);
        Route::get("/user/{id}", [UserController::class, "show"]);
        Route::put("/user/{id}", [UserController::class, "updateBook"]);
        Route::delete("/user/{id}", [UserController::class, "delete"]);

        Route::get("/   publish", [UserController::class, "publishBooks"]);
    });
});
