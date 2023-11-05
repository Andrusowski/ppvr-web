<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;

class HighlightsController
{
    public function getIndex()
    {
        $top_players = DB::table('posts')
            ->select(DB::raw('posts.player_id as id,
                players.name as name,
                SUM(posts.score) as score,
                AVG(posts.score) as avg_score,
                COUNT(posts.id) as posts'))
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->where('posts.created_utc', '>=', (new Carbon('first day of last month'))->timestamp)
            ->where('posts.created_utc', '<=', (new Carbon("last day of last month"))->timestamp)
            ->groupBy('posts.player_id', 'players.name')
            ->orderBy('score', 'desc')
            ->limit(5)
            ->get();

        $top_post_per_player = [];
        foreach ($top_players as $player) {
            $top_post_per_player[$player->name] = Post::wherePlayerId($player->id)->orderBy('score', 'desc')->limit(3)->get();
        }

        $top_authors = DB::table('posts')
            ->select(DB::raw('posts.author as author,
                SUM(posts.score) as score,
                COUNT(posts.id) as posts'))
            ->where('posts.created_utc', '>=', (new Carbon('first day of last month'))->timestamp)
            ->where('posts.created_utc', '<=', (new Carbon("last day of last month"))->timestamp)
            ->groupBy('posts.author')
            ->orderBy('score', 'desc')
            ->limit(5)
            ->get();

        $top_posts = Post::select('posts.*')
            ->addSelect('players.name as player_name')
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->where('posts.created_utc', '>=', (new Carbon('first day of last month'))->timestamp)
            ->where('posts.created_utc', '<=', (new Carbon("last day of last month"))->timestamp)
            ->orderBy('posts.score', 'desc')
            ->limit(5)
            ->get();

        $posts_count = Post::where('posts.created_utc', '>=', (new Carbon('first day of last month'))->timestamp)
            ->where('posts.created_utc', '<=', (new Carbon("last day of last month"))->timestamp)
            ->count();

        $posts_total_score = Post::where('posts.created_utc', '>=', (new Carbon('first day of last month'))->timestamp)
            ->where('posts.created_utc', '<=', (new Carbon("last day of last month"))->timestamp)
            ->sum('score');

        $date = date('F Y', strtotime('last month'));

        Browsershot::url('https://ppvr.andrus.io')->save('highlights.png');

        return view('highlight.highlights')
            ->with('date', $date)
            ->with('top_post_per_player', $top_post_per_player)
            ->with('top_players', $top_players)
            ->with('top_authors', $top_authors)
            ->with('posts_count', $posts_count)
            ->with('posts_total_score', $posts_total_score)
            ->with('top_posts', $top_posts)
            ->render();
    }
}
