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

// 게시물
Route::resource('/articles', 'Api\ArticleController')->except('create', 'edit');

// 인증
Route::group(['prefix' => 'auth'], function () {
    route::post('/login', 'Api\AuthController@login');
    route::post('/logout', 'Api\AuthController@logout');
    route::post('/refresh', 'Api\AuthController@refresh');
    route::post('/me', 'Api\AuthController@me');
});

// fallback route
Route::fallback(function () {
    return response()->json(['message' => 'Not Found'], 404);
});
