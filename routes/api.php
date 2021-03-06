<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/*
|--------------------------------------------------------------------------
| Routes not need AUTH
|--------------------------------------------------------------------------
|
*/

Route::post('login','Auth\AuthController@login');

/*
|--------------------------------------------------------------------------
| Routes need AUTH
|--------------------------------------------------------------------------
|
*/

Route::middleware(['jwt.verify'])->group(function ($router){

    Route::post('users','User\UserController@store');
    Route::get('users','User\UserController@list');
    Route::get('users/{id}','User\UserController@show');
    Route::delete('users/{id}','User\UserController@delete');
    Route::put('users/{id}','User\UserController@edit');
    Route::patch('users/{id}','User\UserController@partialEdit');
});
