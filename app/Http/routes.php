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
Route::get('users/is_created', 'UserController@is_created');
Route::get('users/login_google', 'UserController@login_google');
Route::get('users/disconnect_google', 'UserController@disconnect_google');

Route::get('others/weather', 'OtherController@weather');
Route::get('others/quote', 'OtherController@quote');
Route::get('others/event/{id}', 'OtherController@event');