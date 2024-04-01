<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use App\Models\Post;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HighlightsControllerService
{
    private int $timestampFrom;
    private int $timestampTo;

    public function __construct()
    {
        $this->timestampFrom = (new Carbon('first day of last month'))
            ->setHour(0)
            ->setMinute(0)
            ->setSecond(0)
            ->setTimezone('UTC')
            ->timestamp;
        $this->timestampTo = (new Carbon('last day of last month'))
            ->setHour(23)
            ->setMinute(59)
            ->setSecond(59)
            ->setTimezone('UTC')
            ->timestamp;
    }

    /**
     * @param \Illuminate\Support\Collection $top_players
     * @param Post[] $top_post_per_player
     * @param \Illuminate\Support\Collection $top_authors
     * @param \Illuminate\Support\Collection $top_posts
     *
     * @return string
     */
    public function convertToText(\Illuminate\Support\Collection $top_players, array $top_post_per_player, \Illuminate\Support\Collection $top_authors, \Illuminate\Support\Collection $top_posts): string
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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getTopPlayers(): Collection
    {
        $top_players = DB::table('posts')
                         ->select(DB::raw('posts.player_id as id,
                players.name as name,
                SUM(posts.score) as score,
                AVG(posts.score) as avg_score,
                COUNT(posts.id) as posts'))
                         ->join('players', 'posts.player_id', '=', 'players.id')
                         ->where('posts.created_utc', '>=', $this->timestampFrom)
                         ->where('posts.created_utc', '<=', $this->timestampTo)
                         ->groupBy('posts.player_id', 'players.name')
                         ->orderBy('score', 'desc')
                         ->limit(5)
                         ->get();

        return $top_players;
    }

    /**
     * @param mixed $player
     *
     * @return Post[]|\Illuminate\Support\Collection
     */
    public function getTopPostsForPlayer(mixed $player): Collection
    {
        return Post::wherePlayerId($player->id)
                   ->where('posts.created_utc', '>=', $this->timestampFrom)
                   ->where('posts.created_utc', '<=', $this->timestampTo)
                   ->orderBy('score', 'desc')
                   ->limit(3)
                   ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getTopAuthors(): Collection
    {
        return DB::table('posts')
                 ->select(DB::raw('posts.author as author,
                SUM(posts.score) as score,
                COUNT(posts.id) as posts'))
                 ->where('posts.created_utc', '>=', $this->timestampFrom)
                 ->where('posts.created_utc', '<=', $this->timestampTo)
                 ->groupBy('posts.author')
                 ->orderBy('score', 'desc')
                 ->limit(5)
                 ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getTopPosts(): Collection
    {
        return Post::select('posts.*')
                   ->addSelect('players.name as player_name')
                   ->join('players', 'posts.player_id', '=', 'players.id')
                   ->where('posts.created_utc', '>=', $this->timestampFrom)
                   ->where('posts.created_utc', '<=', $this->timestampTo)
                   ->orderBy('posts.score', 'desc')
                   ->limit(5)
                   ->get();
    }

    /**
     * @return int
     */
    public function getPostsCount(): int
    {
        return Post::where('posts.created_utc', '>=', $this->timestampFrom)
                   ->where('posts.created_utc', '<=', $this->timestampTo)
                   ->count();
    }

    /**
     * @return int|mixed
     */
    public function getPostsTotalScore(): mixed
    {
        return Post::where('posts.created_utc', '>=', $this->timestampFrom)
                   ->where('posts.created_utc', '<=', $this->timestampTo)
                   ->sum('score');
    }

    /**
     * @return object
     */
    public function getUniquePlayersCount(): object
    {
        return DB::table('posts')
                 ->selectRaw('COUNT(DISTINCT player_id) as count')
                 ->where('posts.created_utc', '>=', $this->timestampFrom)
                 ->where('posts.created_utc', '<=', $this->timestampTo)
                 ->first();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getScorePerDay(): Collection
    {
        return DB::table('posts')
                 ->selectRaw('SUM(score) as score_daily, DATE(created_at) as date')
                 ->where('posts.created_utc', '>=', $this->timestampFrom)
                 ->where('posts.created_utc', '<=', $this->timestampTo)
                 ->orderBy('date', 'DESC')
                 ->groupByRaw('date')
                 ->get();
    }
}
