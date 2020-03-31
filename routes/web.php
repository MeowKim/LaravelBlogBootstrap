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
Route::resource('/profile', 'ProfileController');
