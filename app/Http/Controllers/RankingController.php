<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

define("ENTRIES_PER_PAGE", 50);

class RankingController extends Controller
{
    public function getIndexPlayer($sort = 'score')
    {
        $posts = DB::table('posts')
            ->select(DB::raw('posts.player_id,
                              players.name,
                              (SUM(posts.downs)/SUM(posts.ups))*100 as controversy,
                              players.score as score,
                              ' . config('ranking.scoreAvgQuery') . ' as score_avg,
                              COUNT(posts.id) as posts,
                              ranks.rank as lastRank'))
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->rightJoin('ranks', 'posts.player_id', '=', 'ranks.player_id')
            ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
            ->groupBy('posts.player_id', 'players.name', 'ranks.rank')
            ->orderBy($sort, 'desc')
            ->paginate(ENTRIES_PER_PAGE);

        $rank = ENTRIES_PER_PAGE * ($posts->currentPage() - 1);

        $pageUrls = $this->getPageUrls($posts);

        return view('ranking.player')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort)
            ->with('currentPage', $posts->currentPage())
            ->with('pageAmount', $posts->total())
            ->with('pageUrls', $pageUrls);
    }

    public function getIndexAuthor($sort = 'score')
    {
        $posts = DB::table('posts')
            ->select(DB::raw('author,
                              (SUM(downs)/SUM(ups))*100 as controversy,
                              ' . config('ranking.scoreSumQuery') . ' as score,
                              ' . config('ranking.scoreAvgQuery') . ' as score_avg,
                              COUNT(id) as posts'))
            ->where('author', '!=', '[deleted]')
            ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
            ->groupBy('author')
            ->orderBy($sort, 'desc')
            ->paginate(ENTRIES_PER_PAGE);

        $rank = ENTRIES_PER_PAGE * ($posts->currentPage() - 1);

        $pageUrls = $this->getPageUrls($posts);

        return view('ranking.author')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort)
            ->with('currentPage', $posts->currentPage())
            ->with('pageAmount', $posts->total())
            ->with('pageUrls', $pageUrls);
    }

    protected function getPageUrls(\Illuminate\Contracts\Pagination\LengthAwarePaginator $posts): array
    {
        if (( $posts->total() > 7 ) && ( $posts->currentPage() > 5 )) {
            $firstPageRange = ( $posts->currentPage() - 2 > 1 ) ? $posts->currentPage() - 2 : 1;
            $lastPageRange = ( $posts->currentPage() + 2 <= $posts->total() ) ? $posts->currentPage() + 2 : $posts->total();
        } else {
            $firstPageRange = 1;
            $lastPageRange = ( $posts->total() < 7 ) ? $posts->total() : 7;
        }
        $pageUrls = $posts->getUrlRange($firstPageRange, $lastPageRange);

        return $pageUrls;
    }
}
