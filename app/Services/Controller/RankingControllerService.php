<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RankingControllerService
{
    public const ENTRIES_PER_PAGE = 50;

    public function getPageUrls(LengthAwarePaginator $posts): array
    {
        if (( $posts->total() > 7 ) && ( $posts->currentPage() > 5 )) {
            $firstPageRange = ( $posts->currentPage() - 2 > 1 ) ? $posts->currentPage() - 2 : 1;
            $lastPageRange = ( $posts->currentPage() + 2 <= $posts->total() ) ? $posts->currentPage() + 2 : $posts->total();
        } else {
            $firstPageRange = 1;
            $lastPageRange = ( $posts->total() < 7 ) ? $posts->total() : 7;
        }

        return $posts->getUrlRange($firstPageRange, $lastPageRange);
    }

    /**
     * @param string $sort
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPlayerPosts(string $sort): LengthAwarePaginator
    {
        return DB::table('posts')
                 ->select(DB::raw('posts.player_id,
                              players.name,
                              (SUM(posts.downs)/SUM(posts.ups))*100 as controversy,
                              players.score as score,
                              ' . config('ranking.scoreAvgQuery') . ' as score_avg,
                              COUNT(posts.id) as posts,
                              MAX(posts.created_at) as last_created'))
                 ->join('players', 'posts.player_id', '=', 'players.id')
                 ->having('score', '>=', 100)
                 ->groupBy('posts.player_id', 'players.name')
                 ->orderBy($sort, 'desc')
                 ->paginate(static::ENTRIES_PER_PAGE);
    }

    /**
     * @param string $sort
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAuthorPosts(string $sort): LengthAwarePaginator
    {
        return DB::table('posts')
                 ->select(DB::raw('author,
                              (SUM(downs)/SUM(ups))*100 as controversy,
                              authors.score as score,
                              authors.score_weighted as score_weighted,
                              ' . config('ranking.scoreAvgQuery') . ' as score_avg,
                              COUNT(posts.id) as posts,
                              MAX(posts.created_at) as last_created'))
                 ->where('author', '!=', '[deleted]')
                 ->join('authors', 'authors.name', '=', 'posts.author')
                 ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
                 ->groupBy('author')
                 ->orderBy($sort, 'desc')
                 ->paginate(static::ENTRIES_PER_PAGE);
    }
}
