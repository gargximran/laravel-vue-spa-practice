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
    /**
     * All user role management 
     * Add
     * Edit
     * Delete
     * Show
     */
    //show all role
    Route::get('/roles', "RollController@index");


    // store new role
    Route::post('/roles', "RollController@store");

    //update role
    Route::post('/roles/update/{role:id}', "RollController@update");

    //delete role
    Route::delete('roles/delete/{role:id}', "RollController@destroy");




    /**
     * Here the route are for user control
     * all route are protected
     */

    Route::get('logout/{auth}', "UserController@logout");
    Route::get('/verify', 'UserController@verify');
});



Route::post('/login' , "Api\UserController@login");

