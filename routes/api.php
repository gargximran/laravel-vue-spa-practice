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
    Route::post('roles/delete/selected' , "RollController@selectedDestroy");




    /**
     * Here the route are for user control
     * all route are protected
     */

     //Logout controll 
    Route::get('logout/{auth}', "UserController@logout");

    // verify via header authorization the user exist or not
    Route::get('/verify', 'UserController@verify');
});




//Route for login
Route::post('/login' , "Api\UserController@login");

