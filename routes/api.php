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
     * All user management
     * add
     * edit
     * delete
     * show
     */
    Route::get('/users', "UserController@index");
    Route::post('/users', "UserController@store");
    Route::post('users/update/{user}', "UserController@update");
    Route::delete('users/delete/{user}', "UserController@destroy");
    Route::post('users/delete/selected', "UserController@deleteSelected");

    // Change role inline
    Route::post('user/role/change', "UserController@inlineRoleChange");

    //change image inline 
    Route::post('user/image/upload', "UserController@imageUpload");


    //email verify
    Route::post('email/verify', "UserController@emailVerify");




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

