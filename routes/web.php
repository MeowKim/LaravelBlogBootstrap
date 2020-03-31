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

// 블로그 게시물
// Route::group(['prefix' => 'articles'], function () {
//     Route::get('', 'ArticleController@index')->name('articles.index');  // 목록
//     Route::get('create', 'ArticleController@create')->name('articles.create');  // 작성 폼
//     Route::post('', 'ArticleController@store')->name('articles.store'); // 저장
//     Route::get('{id}', 'ArticleController@show')->name('articles.show');    // 상세
//     Route::get('{id}/edit', 'ArticleController@edit')->name('articles.edit');   // 수정 폼
//     Route::put('{id}', 'ArticleController@update')->name('articles.update');    // 업데이트
//     Route::delete('{id}', 'ArticleController@destroy')->name('articles.destroy');   // 삭제
// });

Route::resource('/articles', 'ArticleController');

// 사용자 인증
Auth::routes();
