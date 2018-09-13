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
Route::get('/ranking/author/{sort?}', 'RankingController@getIndexAuthor')->where('sort', 'score|score_avg|controversy|posts');
Route::get('/post/{id}', 'PostController@getIndex');
Route::get('/player/{id}', 'PlayerController@getIndex')->name('profile.player');
Route::get('/author/{name}', 'AuthorController@getIndex');
Route::get('/search', 'SearchController@postSearch');

Auth::routes();

Route::get('/review', 'ReviewController@getIndex')->name('review');
Route::get('/review/add/{id}', 'ReviewController@getAdd');
Route::get('/review/add/{id}', 'ReviewController@getAdd');
Route::post('/review/add/{id}', 'ReviewController@postAdd');
Route::get('/review/delete/{id}', 'ReviewController@getDelete');
