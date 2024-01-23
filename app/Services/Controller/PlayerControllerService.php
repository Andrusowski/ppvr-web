<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use App\Models\Alias;
use App\Models\Player;
use App\Models\Rank;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlayerControllerService
{
    /**
     * @param Player $player
     *
     * @return Alias|null
     */
    public function getLatestAlias(Player $player): Alias|null
    {
        return Alias::where('player_id', '=', $player->id)->orderBy('created_at', 'DESC')->first();
    }

    /**
     * @param int $id
     *
     * @return object
     */
    public function getPlayerStats(int $id): object
    {
        return DB::table('posts')
                 ->select(DB::raw('(SUM(downs)/SUM(ups))*100 as controversy,
                               COUNT(posts.id) as posts'))
                 ->where('player_id', $id)
                 ->first();
    }

    /**
     * @return Player[]|\Illuminate\Support\Collection
     */
    public function getRanking(): Collection|array
    {
        return Player::orderBy('score', 'desc')->get();
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPosts(int $id): Collection
    {
        return DB::table('posts')
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
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getNewPosts(int $id): array
    {
        return DB::table('posts')
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
                 ->get()
                 ->all();
    }

    /**
     * @param Player $player
     *
     * @return Rank[]|\Illuminate\Support\Collection
     */
    public function getRanks(Player $player): Collection|array
    {
        return Rank::where('player_id', '=', $player->id)->orderBy('created_at', 'ASC')->take(92)->get();
    }
}
