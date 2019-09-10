<?php
//SELECT COUNT(player_id) , SUM(score) as score FROM `posts` group by player_id order by score DESC

//SELECT row_number() over (partition by player_id order by score DESC) row_num, score  FROM `posts` order by score DESC
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

define("ENTRIES_PER_PAGE", 50);

class RankingController extends Controller
{
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
                              (SUM(posts.downs)/SUM(posts.ups))*100 as controversy,
                              '.config('ranking.scoreSumQuery').' as score,
                              '.config('ranking.scoreAvgQuery').' as score_avg,
                              COUNT(posts.id) as posts'))
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->join(DB::raw('(
                SELECT row_number() over (partition by player_id order by score DESC) row_num, player_id
                FROM posts 
                order by score DESC) rownum'),
                function($join)
                {
                    $join->on('posts.player_id', '=', 'rownum.player_id');
                })
            ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
            ->groupBy('posts.player_id', 'players.name')
            ->orderBy($sort, 'desc')
            ->paginate(ENTRIES_PER_PAGE);

        $rank = ENTRIES_PER_PAGE * ($posts->currentPage()-1);

        return view('ranking.player')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort);
    }

    public function getIndexAuthor($sort = 'score')
    {
        $posts = DB::table('posts')
            ->select(DB::raw('author,
                              (SUM(downs)/SUM(ups))*100 as controversy,
                              '.config('ranking.scoreSumQuery').' as score,
                              '.config('ranking.scoreAvgQuery').' as score_avg,
                              COUNT(id) as posts'))
            ->where('author', '!=', '[deleted]')
            ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
            ->groupBy('author')
            ->orderBy($sort, 'desc')
            ->paginate(ENTRIES_PER_PAGE);

        $rank = ENTRIES_PER_PAGE * ($posts->currentPage()-1);

        return view('ranking.author')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort);
    }
}
