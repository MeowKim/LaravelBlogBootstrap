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
    return redirect()->route('article.index');
});

Route::group(['prefix' => 'articles'], function () {
    Route::get('', 'ArticleController@index')->name('article.index');  // 목록
    Route::get('create', 'ArticleController@create')->name('article.create');  // 작성 폼
    Route::post('', 'ArticleController@store')->name('article.store'); // 저장
    Route::get('{id}', 'ArticleController@show')->name('article.show');    // 상세
    Route::get('{id}/edit', 'ArticleController@edit')->name('article.edit');   // 수정 폼
    Route::put('{id}', 'ArticleController@update')->name('article.update');    // 업데이트
    Route::delete('{id}', 'ArticleController@destroy')->name('article.destroy');   // 삭제
});