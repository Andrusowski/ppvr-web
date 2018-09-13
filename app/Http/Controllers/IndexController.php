<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
    public function getIndex($sort = 'score')
    {
        $posts = DB::table('posts')
            ->select(DB::raw('posts.player_id,
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

        return view('index')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort);
    }
}
