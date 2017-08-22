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

Route::group(['namespace' => 'Api', 'middleware' => 'auth:api'], function() {
    Route::group(['prefix' => 'account'], function() {
        Route::put('/', 'AccountController@update');
    });

    Route::resource('users', 'UsersController');
    Route::resource('groups', 'GroupsController');
    Route::get('devices/{device}/status', 'DevicesController@status');
    Route::resource('devices', 'DevicesController');
});
