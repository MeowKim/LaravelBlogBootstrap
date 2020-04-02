<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('articles.index');
});

// 인증
Auth::routes();

// 게시물
Route::resource('/articles', 'ArticleController');

// 프로필
Route::group(['prefix' => 'profile'], function () {
    route::get('/', 'ProfileController@index')->name('profile.index');
    route::get('/edit', 'ProfileController@edit')->name('profile.edit');
    route::put('/', 'ProfileController@update')->name('profile.update');
    Route::get('/password/change', 'ProfileController@changePassword')->name('profile.password.change');
    Route::put('/password', 'ProfileController@updatePassword')->name('profile.password.update');
});
