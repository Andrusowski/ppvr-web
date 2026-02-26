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
Route::get('/ranking/player/{sort?}', 'RankingController@getIndexPlayer')->where('sort', 'score|score_weighted|score_avg|posts');
Route::get('/ranking/author/{sort?}', 'RankingController@getIndexAuthor')->where('sort', 'score|score_weighted|score_avg|posts');
Route::get('/post/{id}', 'PostController@getIndex');
Route::get('/player/{id}', 'PlayerController@getIndex')->name('profile.player');
Route::get('/author/{name}', 'AuthorController@getIndex');
Route::post('/search', 'SearchController@postSearch');
Route::get('/changelog', 'ChangelogController@getIndex');
Route::get('/faq', 'FaqController@getIndex');
Route::get('/stats', 'StatsController@getIndex');
Route::get('/legal/notice', 'LegalController@getNotice')->name('legal.notice');
Route::get('/legal/privacy', 'LegalController@getPrivacy')->name('legal.privacy');
Route::get('/highlights', 'HighlightsController@getIndex');
Route::get('/game', 'GameController@getIndex');
Route::post('/game/validate', 'GameController@validateChoice');

// osu! OAuth authentication routes
Route::get('/auth/osu', 'Auth\OsuAuthController@redirect')->name('auth.osu');
Route::get('/auth/osu/callback', 'Auth\OsuAuthController@callback')->name('auth.osu.callback');
Route::post('/logout', 'Auth\OsuAuthController@logout')->name('logout');
Route::get('/auth/me', 'Auth\OsuAuthController@me')->name('auth.me');

// Game stats sync routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/game/stats/sync', 'GameStatsController@sync')->name('game.stats.sync');
    Route::get('/game/stats', 'GameStatsController@get')->name('game.stats.get');
    Route::post('/game/stats/initial-sync', 'GameStatsController@initialSync')->name('game.stats.initial-sync');
    Route::delete('/game/stats', 'GameStatsController@delete')->name('game.stats.delete');
});
