<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $top_posts_per_player = [];
        foreach ($top_players as $player) {
            $top_posts_per_player[$player->name] = Post::wherePlayerId($player->id)->orderBy('score', 'desc')->limit(3)->get();
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

        $unique_players = DB::table('posts')
            ->selectRaw('COUNT(DISTINCT player_id) as count')
            ->where('posts.created_utc', '>=', (new Carbon('first day of last month'))->timestamp)
            ->where('posts.created_utc', '<=', (new Carbon("last day of last month"))->timestamp)
            ->first();

        $score_per_day = DB::table('posts')
            ->selectRaw('SUM(score) as score_daily, DATE(created_at) as date')
            ->where('posts.created_utc', '>=', (new Carbon('first day of last month'))->timestamp)
            ->where('posts.created_utc', '<=', (new Carbon("last day of last month"))->timestamp)
            ->orderBy('date', 'DESC')
            ->groupByRaw('date')
            ->get();

        $text = $this->convertToText($top_players, $top_posts_per_player, $top_authors, $top_posts);

        $date = date('F Y', strtotime('last month'));

        return view('highlight.highlights')
            ->with('date', $date)
            ->with('top_posts_per_player', $top_posts_per_player)
            ->with('top_players', $top_players)
            ->with('top_authors', $top_authors)
            ->with('posts_count', $posts_count)
            ->with('posts_total_score', $posts_total_score)
            ->with('top_posts', $top_posts)
            ->with('unique_players', $unique_players)
            ->with('score_per_day', $score_per_day)
            ->with('text', $text);
    }

    /**
     * @param \Illuminate\Support\Collection $top_players
     * @param Post[] $top_post_per_player
     * @param \Illuminate\Support\Collection $top_authors
     * @param \Illuminate\Support\Collection $top_posts
     *
     * @return string
     */
    private function convertToText(\Illuminate\Support\Collection $top_players, array $top_post_per_player, \Illuminate\Support\Collection $top_authors, \Illuminate\Support\Collection $top_posts): string
    {
        $player_rank = 0;
        $text = '# Top players:' . PHP_EOL;
        foreach ($top_players as $player) {
            $text .= sprintf(PHP_EOL . '## \#%d [%s](https://osu.ppy.sh/users/%s) (%d Score, %d Avg. Score, %d Posts)', ++$player_rank, $player->name, $player->id, $player->score, $player->avg_score, $player->posts) . PHP_EOL;
            foreach ($top_post_per_player[$player->name] as $post) {
                $text .= sprintf('* [%s](https://www.reddit.com/r/osugame/comments/%s) (%d points)', $post->map_title . ' [' . $post->map_diff . ']', $post->id, $post->score) . PHP_EOL;
            }
        }

        $author_rank = 0;
        $text .= PHP_EOL . '# Top authors:' . PHP_EOL;
        foreach ($top_authors as $author) {
            $text .= sprintf('%d. [%s](https://www.reddit.com/user/%s) (%d Score)', ++$author_rank, $author->author, $author->author, $author->score) . PHP_EOL;
        }

        $post_rank = 0;
        $text .= PHP_EOL . '# Top posts:' . PHP_EOL;
        foreach ($top_posts as $post) {
            $text .= sprintf('%d. [%s](https://www.reddit.com/r/osugame/comments/%s) (%d Score)', ++$post_rank, $post->map_title . ' [' . $post->map_diff . ']', $post->id, $post->score) . PHP_EOL;
        }

        $text .= PHP_EOL . '---' . PHP_EOL . 'The all-time ranking can be found at [PPvR](https://ppvr.andrus.io)';

        return $text;
    }
}
