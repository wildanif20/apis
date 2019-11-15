<?php

use Illuminate\Http\Request;

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

 


//Auth Controller
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {

    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    
    //Middleware cara lain
    Route::get('me', 'AuthController@me')->middleware('api.auth');


    //Middleware
    Route::group(['middleware' => 'api.auth'], function () {
        Route::post('logout', 'AuthController@logout');
        Route::put('update_name', 'AuthController@updatename');
        Route::post('refresh', 'AuthController@refresh');     
        Route::post('change_password', 'AuthController@changepassword');
    });

});