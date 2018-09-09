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

Route::get('/', 'RankingSelectController@getIndex');
Route::get('/ranking/{sort?}', 'SumRankingController@getIndex');
Route::get('/post', 'PostController@getIndex');
Route::get('/player', 'PlayerController@getIndex');
Route::get('/author', 'AuthorController@getIndex');

Auth::routes();

Route::get('/review', 'ReviewController@getIndex')->name('review');
Route::get('/review/add/{id}', 'ReviewController@getAdd')->name('review');
Route::get('/review/add/{id}', 'ReviewController@getAdd')->name('review');
Route::post('/review/add/{id}', 'ReviewController@postAdd')->name('review');
Route::get('/review/delete/{id}', 'ReviewController@getDelete')->name('review');
