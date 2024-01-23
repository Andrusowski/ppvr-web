<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class IndexControllerService
{
    private const KEY_CACHE_INDEX_PLAYER_RANKING = 'index_player_ranking';
    private const KEY_CACHE_INDEX_AUTHOR_RANKING = 'index_author_ranking';
    private const KEY_CACHE_INDEX_NEW_POSTS = 'index_new_posts';

    public function getPlayerRanking(): Collection
    {
        return Cache::remember(static::KEY_CACHE_INDEX_PLAYER_RANKING, now()->addMinutes(5), function () {
            return DB::table('posts')
                     ->select(DB::raw('posts.player_id,
                    players.name,
                    players.score as score,
                    COUNT(posts.id) as posts'))
                     ->join('players', 'posts.player_id', '=', 'players.id')
                     ->groupBy('posts.player_id', 'players.name')
                     ->orderBy('score', 'desc')
                     ->take(5)
                     ->get();
        });
    }

    public function getAuthorRanking(): Collection
    {
        return Cache::remember(static::KEY_CACHE_INDEX_AUTHOR_RANKING, now()->addMinutes(5), function () {
            return DB::table('posts')
                     ->select(DB::raw('author,
                    authors.score as score,
                    COUNT(posts.id) as posts'))
                     ->where('author', '!=', '[deleted]')
                     ->join('authors', 'authors.name', '=', 'posts.author')
                     ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
                     ->groupBy('author')
                     ->orderBy('score', 'desc')
                     ->take(5)
                     ->get();
        });
    }

    public function getNewPosts(): Collection
    {
        return Cache::remember(static::KEY_CACHE_INDEX_NEW_POSTS, now()->addMinutes(5), function () {
            return DB::table('posts')
                     ->select(DB::raw('posts.id,
                    players.name,
                    posts.map_artist,
                    posts.map_title,
                    posts.map_diff,
                    posts.score as score,
                    posts.created_utc'))
                     ->join('players', 'posts.player_id', '=', 'players.id')
                     ->groupBy('posts.id')
                     ->orderBy('posts.created_utc', 'desc')
                     ->take(20)
                     ->get();
        });
    }
}
