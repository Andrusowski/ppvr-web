<?php

/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HighlightsControllerService
{
    private int $timestampFrom;
    private int $timestampTo;

    private int $previousTimestampFrom;
    private int $previousTimestampTo;

    public function __construct()
    {
        $timestampFrom = (new Carbon('first day of last month'))
            ->setHour(0)
            ->setMinute(0)
            ->setSecond(0)
            ->setTimezone('UTC');

        $this->timestampFrom = $timestampFrom->timestamp;
        $this->timestampTo = (clone $timestampFrom)->endOfMonth()->timestamp;
        $this->previousTimestampFrom = (clone $timestampFrom)
            ->subMonth()
            ->timestamp;
        $this->previousTimestampTo = (clone $timestampFrom)
            ->subMonth()
            ->endOfMonth()
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

    public function getTopPlayers(bool $previousTimeframe = false): Collection
    {
        $top_players = DB::table('posts')
            ->select(DB::raw('posts.player_id as id,
                players.name as name,
                SUM(posts.score) as score,
                AVG(posts.score) as avg_score,
                COUNT(posts.id) as posts'))
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->groupBy('posts.player_id', 'players.name')
            ->orderBy('score', 'desc')
            ->limit(5)
            ->get();

        return $top_players;
    }

    public function getTopPostsForPlayer(mixed $player, bool $previousTimeframe = false): Collection
    {
        return Post::wherePlayerId($player->id)
            ->where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->orderBy('score', 'desc')
            ->limit(3)
            ->get();
    }

    public function getTopAuthors(bool $previousTimeframe = false): Collection
    {
        return DB::table('posts')
            ->select(DB::raw('posts.author as author,
                SUM(posts.score) as score,
                COUNT(posts.id) as posts'))
            ->where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->groupBy('posts.author')
            ->orderBy('score', 'desc')
            ->limit(5)
            ->get();
    }

    public function getTopPosts(bool $previousTimeframe = false): Collection
    {
        return Post::select('posts.*')
            ->addSelect('players.name as player_name')
            ->join('players', 'posts.player_id', '=', 'players.id')
            ->where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->orderBy('posts.score', 'desc')
            ->limit(5)
            ->get();
    }

    public function getPostsCount(bool $previousTimeframe = false): int
    {
        return Post::where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->count();
    }

    public function getPostsTotalScore(bool $previousTimeframe = false): mixed
    {
        return Post::where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->sum('score');
    }

    public function getUniquePlayersCount(bool $previousTimeframe = false): object
    {
        return DB::table('posts')
            ->selectRaw('COUNT(DISTINCT player_id) as count')
            ->where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->first();
    }

    public function getScorePerDay(bool $previousTimeframe = false): Collection
    {
        return DB::table('posts')
            ->selectRaw('SUM(score) as score_daily, DATE(created_at) as date')
            ->where('posts.created_utc', '>=', $this->getTimestampFrom($previousTimeframe))
            ->where('posts.created_utc', '<=', $this->getTimestampTo($previousTimeframe))
            ->orderBy('date', 'DESC')
            ->groupByRaw('date')
            ->get();
    }

    private function getTimestampFrom(bool $previousTimeframe = false): int
    {
        return $previousTimeframe ? $this->previousTimestampFrom : $this->timestampFrom;
    }

    private function getTimestampTo(bool $previousTimeframe = false): int
    {
        return $previousTimeframe ? $this->previousTimestampTo : $this->timestampTo;
    }
}
