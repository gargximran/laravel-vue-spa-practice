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

Route::group(['middleware' => ['auth:sanctum'], 'namespace' => 'Api'], function(){
    Route::get('/roles', "RollController@index");
    Route::post('/roles', "RollController@store");
    Route::post('/roles/update/{role:id}', "RollController@update");
    Route::delete('roles/delete/{role:id}', "RollController@destroy");
    Route::get('/verify', 'UserController@verify');
});



Route::post('/login' , "Api\UserController@login");

