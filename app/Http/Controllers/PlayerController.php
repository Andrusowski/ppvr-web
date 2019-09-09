<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Player;

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

        $player_stats = DB::table('posts')
            ->select(DB::raw('(SUM(downs)/SUM(ups))*100 as controversy,
                               '.config('ranking.scoreSumQuery').' as score,
                               '.config('ranking.scoreAvgQuery').' as score_avg,
                               COUNT(posts.id) as posts'))
            ->where('player_id', $id)
            ->first();

        $ranking = DB::table('posts')
            ->select(DB::raw('player_id,
                              '.config('ranking.scoreSumQuery').' as score'))
            ->groupBy('player_id')
            ->orderBy('score', 'desc')
            ->get();

        $rank = 1;
        foreach ($ranking as $rankingPlayer) {
            if ($rankingPlayer->player_id != $player->id) {
                $rank++;
            }
            else {
                break;
            }
        }

        $posts = DB::table('posts')
            ->select(DB::raw('id,
                              map_artist,
                              map_title,
                              map_diff,
                              '.config('ranking.scoreSumQuery').' as score,
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
                              '.config('ranking.scoreSumQuery').' as score,
                              (downs/ups)*100 as controversy,
                              created_utc'))
            ->where('player_id', $id)
            ->groupBy('posts.id')
            ->orderBy('created_utc', 'desc')
            ->take(10)
            ->get();

        return view('profile.player')
            ->with('rank', $rank)
            ->with('posts', $posts)
            ->with('player', $player)
            ->with('posts_new', $posts_new)
            ->with('player_stats', $player_stats);
    }
}
