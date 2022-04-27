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

use Illuminate\Support\Facades\Route;

Route::get('/', 'IndexController@getIndex');
Route::get('/ranking/player/{sort?}', 'RankingController@getIndexPlayer')->where('sort', 'score|score_avg|controversy|posts');
Route::get('/ranking/author/{sort?}', 'RankingController@getIndexAuthor')->where('sort', 'score|score_avg|controversy|posts');
Route::get('/post/{id}', 'PostController@getIndex');
Route::get('/player/{id}', 'PlayerController@getIndex')->name('profile.player');
Route::get('/author/{name}', 'AuthorController@getIndex');
Route::get('/search', 'SearchController@postSearch');
Route::get('/changelog', 'ChangelogController@getIndex');
Route::get('/faq', 'FaqController@getIndex');
Route::get('/stats', 'StatsController@getIndex');
