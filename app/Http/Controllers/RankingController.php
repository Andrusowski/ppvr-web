<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    private $scoreSumQuery = 'SUM(posts.ups*(1+((posts.gilded)*0.1)))-SUM(posts.downs)';
    private $scoreAvgQuery = 'AVG(posts.ups*(1+((posts.gilded)*0.1)))-AVG(posts.downs)';

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndexPlayer($sort = 'score')
    {
        $posts = DB::table('posts')
            ->select(DB::raw('posts.player_id,
                              players.name,
                              (SUM(posts.downs)/SUM(posts.ups))*100 as controversy,'
                              .$this->scoreSumQuery.' as score,'
                              .$this->scoreAvgQuery.' as score_avg,
                              COUNT(posts.id) as posts'))
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->having(DB::raw($this->scoreSumQuery), '>=', 100)
            ->groupBy('posts.player_id', 'players.name')
            ->orderBy($sort, 'desc')
            ->paginate(50);

        $rank = 15 * ($posts->currentPage()-1);

        return view('ranking.player')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort);
    }

    public function getIndexAuthor($sort = 'score')
    {
        $posts = DB::table('posts')
            ->select(DB::raw('author,
                              (SUM(downs)/SUM(ups))*100 as controversy,'
                              .$this->scoreSumQuery.' as score,'
                              .$this->scoreAvgQuery.' as score_avg,
                              COUNT(id) as posts'))
            ->where('author', '!=', '[deleted]')
            ->having(DB::raw($this->scoreSumQuery), '>=', 100)
            ->groupBy('author')
            ->orderBy($sort, 'desc')
            ->paginate(15);

        $rank = 15 * ($posts->currentPage()-1);

        return view('ranking.author')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort);
    }
}
