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

// AUTH
Route::post('/user/login', 'AuthController@login');
Route::post('/user/login/token', 'AuthController@loginByToken');
Route::post('/user/is/token/valid', 'AuthController@isTokenValid');

// USER
Route::post('/user/create', 'UserController@create');
Route::post('/user/set', 'UserController@set');
Route::post('/user/delete', 'UserController@delete');
Route::post('/user/get', 'UserController@get');
Route::post('/user/change/password', 'UserController@changePassword');
Route::post('/user/set/default/password', 'UserController@setDefaultPasswordToUserWithId');

// SETTING
Route::post('/setting/set', 'SettingController@set');
Route::post('/setting/get', 'SettingController@get');

// ACTION
Route::post('/action/set', 'ActionController@set');
Route::post('/action/get', 'ActionController@get');
Route::post('/list/roles', 'ActionController@getListOfRoles');
Route::post('/list/actions', 'ActionController@getListOfActions');

// ARTICLE
Route::post('/article/set', 'ArticleController@set');
Route::post('/article/get', 'ArticleController@get');
Route::post('/article/delete', 'ArticleController@delete');
Route::post('/article/search', 'ArticleController@search');

// TAG
Route::post('/tag/create', 'TagController@create');
Route::post('/tag/delete', 'TagController@delete');
Route::post('/tag/list', 'TagController@list');
Route::post('/tag/remove', 'TagController@remove');
Route::post('/tag/add', 'TagController@add');
