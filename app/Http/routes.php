<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('users', 'UserController@index');
Route::get('users/insert', 'UserController@insertTest');
Route::get('users/check', 'UserController@check');
Route::get('users/create', 'UserController@create_get');
Route::post('users/create', 'UserController@create_post');