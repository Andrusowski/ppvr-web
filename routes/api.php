<?php

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

use App\Http\Resources\PlayerCollection;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Http\Resources\RankCollection;
use App\Models\Player;
use App\Models\Post;
use App\Models\Rank;
use Illuminate\Support\Facades\Route;

Route::get('/players', function () {
    return new PlayerCollection(Player::paginate());
});
Route::get('/players/{id}', function ($id) {
    return new PlayerResource(Player::findOrFail($id));
});

Route::get('/posts', function () {
    return new PostCollection(Post::paginate());
});
Route::get('/posts/{id}', function ($id) {
    return new PostResource(Post::findOrFail($id));
});

Route::get('/ranks/{playerId}', function ($playerId) {
    return new RankCollection(Rank::wherePlayerId($playerId)->orderByDesc('created_at')->get());
});
