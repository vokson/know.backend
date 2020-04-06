<?php

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

// USER
Route::post('/user/create', 'UserController@create');
Route::post('/user/set', 'UserController@set');
Route::post('/user/delete', 'UserController@delete');
Route::post('/user/get', 'UserController@get');

// SETTING
Route::post('/setting/set', 'SettingController@set');
Route::post('/setting/get', 'SettingController@get');
