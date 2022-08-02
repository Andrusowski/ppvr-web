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

/**
 * Players
 *
 * Get all players (paginated, 50 per page)
 *
 * @group Players
 *
 * @apiResourceCollection App\Http\Resources\PlayerCollection
 * @apiResourceModel App\Models\Player paginate=50
 */
Route::get('/players', function () {
    return new PlayerCollection(Player::paginate(50));
})->name('api/players');
/**
 * Player
 *
 * Get a player by id (same as osu player id)
 *
 * @group Players
 *
 * @apiResource App\Http\Resources\PlayerResource
 * @apiResourceModel App\Models\Player
 * @urlParam id integer required player id (same as osu player id) Example: 124493
 */
Route::get('/players/{id}', function ($id) {
    return new PlayerResource(Player::findOrFail($id));
})->name('api/player-by-id');

/**
 * Posts
 *
 * Get all posts (paginated, 50 per page)
 *
 * @group Posts
 *
 * @apiResourceCollection App\Http\Resources\PostCollection
 * @apiResourceModel App\Models\Post paginate=50
 */
Route::get('/posts', function () {
    return new PostCollection(Post::paginate(50));
})->name('api/posts');

/**
 * Post
 *
 * Get a post by id (same as reddit post id)
 *
 * @group Posts
 *
 * @apiResource App\Http\Resources\PostResource
 * @apiResourceModel App\Models\Post
 * @urlParam id string required post id (same as reddit post id) Example: u3rk97
 */
Route::get('/posts/{id}', function ($id) {
    return new PostResource(Post::findOrFail($id));
})->name('api/post-by-id');

/**
 * Posts by Player
 *
 * Get a players' posts by player id (same as osu player id)
 *
 * @group Posts
 *
 * @apiResource App\Http\Resources\PostResource
 * @apiResourceModel App\Models\Post
 * @urlParam id integer required player id (same as osu player id) Example: 124493
 */
Route::get('/posts/by-player/{id}', function ($id) {
    return new PostResource(Player::findOrFail($id)->post()->paginate(50));
})->name('api/posts-by-player');

/**
 * Ranks
 *
 * Get the rank history for a player
 *
 * @group Ranks
 *
 * @apiResourceCollection App\Http\Resources\RankCollection
 * @apiResourceModel App\Models\Rank
 * @urlParam playerId integer required player id (same as osu player id) Example: 124493
 */
Route::get('/ranks/{playerId}', function ($playerId) {
    return new RankCollection(Rank::wherePlayerId($playerId)->orderByDesc('created_at')->get());
})->name('api/ranks-by-player');
