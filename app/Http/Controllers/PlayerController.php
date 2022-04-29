<?php

namespace App\Http\Controllers;

use App\Models\Alias;
use App\Models\Player;
use App\Models\Rank;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    //'.config('ranking.scoreSumQuery').'
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex($id)
    {
        $player = Player::find($id);
        if (!$player) {
            abort(404);
        }

        $alias = Alias::where('player_id', '=', $player->id)->orderBy('created_at', 'DESC')->first();

        $player_stats = DB::table('posts')
            ->select(DB::raw('(SUM(downs)/SUM(ups))*100 as controversy,
                               ' . config('ranking.scoreAvgQuery') . ' as score_avg,
                               COUNT(posts.id) as posts'))
            ->where('player_id', $id)
            ->first();

        $ranking = Player::orderBy('score', 'desc')->get();

        $rank = 1;
        foreach ($ranking as $rankingPlayer) {
            if ($rankingPlayer->id != $player->id) {
                $rank++;
            } else {
                break;
            }
        }

        $awards = DB::table('posts')
            ->select(DB::raw('SUM(silver) as silver,
                               SUM(gold) as gold,
                               SUM(platinum) as platinum'))
            ->where('player_id', $player->id)
            ->get();

        $posts = DB::table('posts')
            ->select(DB::raw('id,
                              map_artist,
                              map_title,
                              map_diff,
                              ' . config('ranking.scoreSumQuery') . ' as score,
                              (downs/ups)*100 as controversy'))
            ->where('player_id', $id)
            ->groupBy('posts.id')
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        $posts_new = DB::table('posts')
            ->select(DB::raw('id,
                              map_artist,
                              map_title,
                              map_diff,
                              ' . config('ranking.scoreSumQuery') . ' as score,
                              (downs/ups)*100 as controversy,
                              created_utc'))
            ->where('player_id', $id)
            ->groupBy('posts.id')
            ->orderBy('created_utc', 'desc')
            ->take(10)
            ->get();

        $ranks = Rank::where('player_id', '=', $player->id)->orderBy('created_at', 'ASC')->take(92)->get();

        return view('profile.player')
            ->with('rank', $rank)
            ->with('posts', $posts)
            ->with('player', $player)
            ->with('alias', $alias)
            ->with('awards', $awards[0])
            ->with('posts_new', $posts_new)
            ->with('player_stats', $player_stats)
            ->with('ranks', $ranks);
    }
}
