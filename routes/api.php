<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\API\Auth\AuthController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', [AuthController::class, 'signin'])->middleware();
Route::resource("books", BookController::class);
Route::group(["prefix"=>"todo"],function(){
    Route::get("/get/{id}",[TodoController::class,"get"]);
    Route::get("/gets",[TodoController::class,"gets"]);
    Route::post("/store",[TodoController::class,"store"]);
    Route::put("/update/{id}",[TodoController::class,"update"]);
    Route::delete("/delete/{id}",[TodoController::class,"delete"]);
});