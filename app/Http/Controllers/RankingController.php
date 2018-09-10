<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\Player;

class RankingController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndexPlayer($sort = 'score')
    {
        $posts = DB::table('posts')->select(DB::raw('posts.player_id,
                                                     players.name,
                                                     (SUM(posts.downs)/SUM(posts.ups))*100 as controversy,
                                                     SUM(posts.score*(1+((posts.gilded)*0.1))) as score,
                                                     AVG(posts.score*(1+((posts.gilded)*0.1))) as score_avg,
                                                     COUNT(posts.id) as posts'))
                                   ->join('players', 'posts.player_id', '=', 'players.id')
                                   ->groupBy('posts.player_id', 'players.name')
                                   ->orderBy($sort, 'desc')
                                   ->paginate(15);

        $rank = 15 * ($posts->currentPage()-1);

        return view('ranking.player')->with('posts', $posts)
                              ->with('rank', $rank)
                              ->with('sort', $sort);
    }

    public function getIndexAuthor($sort = 'score')
    {
        $posts = DB::table('posts')->select(DB::raw('author,
                                                     (SUM(downs)/SUM(ups))*100 as controversy,
                                                     SUM(score*(1+((gilded)*0.1))) as score,
                                                     AVG(score*(1+((gilded)*0.1))) as score_avg,
                                                     COUNT(id) as posts'))
                                   ->where('author', '!=', '[deleted]')
                                   ->groupBy('author')
                                   ->orderBy($sort, 'desc')
                                   ->paginate(15);

        $rank = 15 * ($posts->currentPage()-1);

        return view('ranking.author')->with('posts', $posts)
                              ->with('rank', $rank)
                              ->with('sort', $sort);
    }
}
