<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['middleware' => 'auth'], function() {
    Route::get('devices/{device}/download_key', 'DevicesController@downloadKey');
    Route::get('users/{user}/download_key', 'UsersController@downloadKey');
    Route::get('/vpn/download/{hash}', 'VpnKeyController@download');
});

Route::group(['prefix' => 'auth'], function() {
    Route::get('/', function () {
        if (\Auth::check()) {
            return redirect()->to('/');
        }
        return view('layouts/auth');
    });
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('/check', 'Auth\LoginController@check');
    Route::get('/logout', 'Auth\LoginController@logout');
});

Route::any('/{any}', function () {
    $role = auth()->user()->role;
    return view('layouts.app', ['role' => $role]);
})->where('any', '(?!build).*')->middleware('auth');
