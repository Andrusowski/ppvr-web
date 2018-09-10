<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\Player;

class PlayerController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex($id)
    {
        $player = Player::find($id);
        $player_stats = DB::table('posts')->select(DB::raw('(SUM(downs)/SUM(ups))*100 as controversy,
                                                            SUM(score*(1+((gilded)*0.1))) as score,
                                                            AVG(score*(1+((gilded)*0.1))) as score_avg,
                                                            COUNT(posts.id) as posts'))
                                   ->where('player_id', $id)
                                   ->first();
        $posts = DB::table('posts')->select(DB::raw('id,
                                                     map_artist,
                                                     map_title,
                                                     map_diff,
                                                     score*(1+((gilded)*0.1)) as score,
                                                     (downs/ups)*100 as controversy'))
                                   ->where('player_id', $id)
                                   ->orderBy('score', 'desc')
                                   ->paginate(15);

        $rank = 15 * ($posts->currentPage()-1);

        return view('player')->with('posts', $posts)
                             ->with('player', $player)
                             ->with('player_stats', $player_stats);
    }
}
