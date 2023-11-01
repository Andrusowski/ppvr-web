<?php

namespace App\Http\Controllers;

use App\Services\RedditService;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function getIndex()
    {
        $rank_players = 0;
        $posts_players = DB::table('posts')
            ->select(DB::raw('posts.player_id,
                              players.name,
                              (SUM(posts.downs)/SUM(posts.ups))*100 as controversy,
                              players.score as score,
                              ' . config('ranking.scoreAvgQuery') . ' as score_avg,
                              COUNT(posts.id) as posts'))
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->groupBy('posts.player_id', 'players.name')
            ->orderBy('score', 'desc')
            ->take(5)
            ->get();

        $rank_authors = 0;
        $posts_authors = DB::table('posts')
            ->select(DB::raw('author,
                              (SUM(downs)/SUM(ups))*100 as controversy,
                              authors.score as score,
                              ' . config('ranking.scoreAvgQuery') . ' as score_avg,
                              COUNT(posts.id) as posts'))
            ->where('author', '!=', '[deleted]')
            ->join('authors', 'authors.name', '=', 'posts.author')
            ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
            ->groupBy('author')
            ->orderBy('score', 'desc')
            ->take(5)
            ->get();

        $posts_new = DB::table('posts')
            ->select(DB::raw('posts.id,
                              players.name,
                              posts.map_artist,
                              posts.map_title,
                              posts.map_diff,
                              posts.score as score,
                              (posts.downs/posts.ups)*100 as controversy,
                              posts.created_utc'))
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->groupBy('posts.id')
            ->orderBy('posts.created_utc', 'desc')
            ->take(20)
            ->get();

        //find top new post
        $posts_new_top = 0;
        $posts_new_top_score = 0;
        $postsCount = count($posts_new);
        for ($i = 0; $i < $postsCount; $i++) {
            if ($posts_new[$i]->score > $posts_new_top_score) {
                $posts_new_top = $i;
                $posts_new_top_score = $posts_new[$i]->score;
            }
        }

        if ($posts_new_top_score >= 100) {
            //get top comment from top post
            $topComment = RedditService::getTopCommentForPost($posts_new[$posts_new_top]->id);
        }

        return view('index')
            ->with('rank_players', $rank_players)
            ->with('posts_players', $posts_players)
            ->with('rank_authors', $rank_authors)
            ->with('posts_authors', $posts_authors)
            ->with('posts_new', $posts_new)
            ->with('top_comment', $topComment ?? null);
    }
}
