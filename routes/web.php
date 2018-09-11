<?php

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

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('/', 'IndexController@getIndex');
Route::get('/ranking', 'RawRankingController@getIndex');
Route::get('/ranking/player/{sort?}', 'RankingController@getIndexPlayer')->where('sort', 'score|score_avg|controversy|posts');
Route::get('/ranking/weighted/{sort?}', 'WeightedRankingController@getIndex');
Route::get('/ranking/author/{sort?}', 'RankingController@getIndexAuthor')->where('sort', 'score|score_avg|controversy|posts');
Route::get('/post/{id}', 'PostController@getIndex');
Route::get('/player/{id}', 'PlayerController@getIndex');
Route::get('/author/{name}', 'AuthorController@getIndex');

Auth::routes();

Route::get('/review', 'ReviewController@getIndex')->name('review');
Route::get('/review/add/{id}', 'ReviewController@getAdd')->name('review');
Route::get('/review/add/{id}', 'ReviewController@getAdd')->name('review');
Route::post('/review/add/{id}', 'ReviewController@postAdd')->name('review');
Route::get('/review/delete/{id}', 'ReviewController@getDelete')->name('review');
