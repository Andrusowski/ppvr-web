<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AuthorControllerService
{
    /**
     * @param string $name
     *
     * @return object
     */
    public function getAuthorStats(string $name): object
    {
        return DB::table('posts')
                 ->select(DB::raw('author,
                             (SUM(downs)/SUM(ups))*100 as controversy,
                             COUNT(posts.id) as posts'))
                 ->where('author', $name)
                 ->groupBy('author')
                 ->first();
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getRanking(string $name): array
    {
        return DB::select("
            SELECT name, ranking.number
            FROM (
                SELECT name, RANK() OVER (ORDER BY score DESC) as number
                FROM authors
            ) as ranking
            WHERE name=:name
        ", [ 'name' => $name ]);
    }

    /**
     * @param string $name
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPosts(string $name): Collection
    {
        return DB::table('posts')
                 ->select(DB::raw('id,
                              map_artist,
                              map_title,
                              map_diff,
                              score,
                              (downs/ups)*100 as controversy'))
                 ->where('author', $name)
                 ->orderBy('score', 'desc')
                 ->take(10)
                 ->get();
    }

    /**
     * @param string $name
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNewPosts(string $name): Collection
    {
        return DB::table('posts')
                 ->select(DB::raw('id,
                              map_artist,
                              map_title,
                              map_diff,
                              score,
                              (downs/ups)*100 as controversy,
                              created_utc'))
                 ->where('author', $name)
                 ->orderBy('created_utc', 'desc')
                 ->take(10)
                 ->get();
    }
}
