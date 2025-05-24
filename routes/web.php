<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get("/run", function () {
    return view("run");
});
Route::post("/run2", function () {
    // $command = request()->command;
    // $ex = '';
    // $e = 
    // exec($command, $ex , $e);
    // print_r($ex);
    // print_r($e);

    echo \Illuminate\Support\Facades\Artisan::call(request()->command);
    echo "<br>";
    echo "Done ".request()->command ;
})->name("run_command");
