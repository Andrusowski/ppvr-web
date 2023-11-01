<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services;

use App\Models\Author;
use App\Models\Player;
use App\Models\Rank;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Style\OutputStyle;

class ScoreService
{
    public static function updatePlayerScore(?string $onlyPlayerId = null, ?OutputStyle $output = null)
    {
        $playerIds = [];

        if ($onlyPlayerId) {
            $playerIds[] = $onlyPlayerId;
        } else {
            $playerCollection = Player::all();
            foreach ($playerCollection as $player) {
                $playerIds[] = $player->id;
            }
        }

        $bar = $output?->createProgressBar(count($playerIds));
        DB::beginTransaction();

        foreach ($playerIds as $playerId) {
            DB::statement('
            UPDATE players
            JOIN (
                SELECT player_id, SUM(round((score+(platinum*180)+(gold*50)+(silver*10)) * POWER(0.95, row_num - 1))) AS weighted
                FROM (
                    SELECT row_number() over (partition BY player_id ORDER BY score DESC) row_num, score, silver, gold, platinum, player_id
                    FROM posts
                    ORDER BY score DESC
                ) AS ranking
                GROUP BY player_id
                ORDER BY weighted DESC
            ) weighted ON players.id=weighted.player_id
            SET score=weighted.weighted
            WHERE players.id=' . $playerId);

            $bar?->advance();
        }

        DB::commit();
        $bar?->finish();
    }

    public static function updateAuthorScore(?string $onlyAuthorName = null, ?OutputStyle $output = null)
    {
        $authorNames = [];

        if ($onlyAuthorName) {
            $authorNames[] = $onlyAuthorName;
        } else {
            $authors = Author::all();

            foreach ($authors as $author) {
                $authorNames[] = $author->name;
            }
        }

        $bar = $output?->createProgressBar(count($authorNames));
        DB::beginTransaction();

        foreach ($authorNames as $authorName) {
            // create author if not exists
            Author::whereName($authorName)->firstOrCreate(['name' => $authorName]);

            DB::statement('
            UPDATE authors
            JOIN (
                SELECT author, SUM(round((score+(platinum*180)+(gold*50)+(silver*10)) * POWER(0.95, row_num - 1))) AS weighted
                FROM (
                    SELECT row_number() over (partition BY author ORDER BY score DESC) row_num, score, silver, gold, platinum, author
                    FROM posts
                    ORDER BY score DESC
                ) AS ranking
                GROUP BY author
                ORDER BY weighted DESC
            ) weighted ON authors.name=weighted.author
            SET score=weighted.weighted
            WHERE authors.name="' . $authorName . '"');

            $bar?->advance();
        }

        DB::commit();
        $bar?->finish();
    }

    public static function savePlayerRanks(?string $definedTimestamp, ?OutputStyle $output = null)
    {
        $players = Player::orderBy('score', 'DESC')->get();
        $bar = $output?->createProgressBar($players->count());

        DB::beginTransaction();

        foreach ($players as $rankNr => $player) {
            $rank = new Rank();
            $rank->player_id = $player->id;
            $rank->rank = ($rankNr + 1);
            if ($definedTimestamp !== null) {
                $rank->created_at = $definedTimestamp;
                $rank->updated_at = $definedTimestamp;
            }
            $rank->save();

            $bar?->advance();
        }

        $month_seconds = 2629743;
        $expireTimestamp = time() - ($month_seconds * 3);
        DB::table('ranks')->where('created_at', '<', gmdate("Y-m-d", $expireTimestamp))->delete();

        DB::commit();
        $bar?->finish();
    }
}
