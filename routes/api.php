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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 게시물
Route::resource('articles', 'Api\ArticleController')->except('create', 'edit');

// 인증
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    route::post('login', 'AuthController@login');
    route::post('logout', 'AuthController@logout');
    route::post('refresh', 'AuthController@refresh');
    route::post('me', 'AuthController@me');
});
