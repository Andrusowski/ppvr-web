<?php

/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Http\Controllers;

use App\Services\Controller\HighlightsControllerService;
use App\Services\RedditService;

class HighlightsController
{
    public function getIndex()
    {
        date_default_timezone_set('Etc/UCT');
        $controllerService = new HighlightsControllerService();

        $topPlayers = $controllerService->getTopPlayers();
        $topPlayersPrevious = $controllerService->getTopPlayers(true);
        $topPostsPerPlayer = [];
        foreach ($topPlayers as $player) {
            $topPostsPerPlayer[$player->name] = $controllerService->getTopPostsForPlayer($player);
        }
        $topPostsPerPlayerPrevious = [];
        foreach ($topPlayersPrevious as $player) {
            $topPostsPerPlayerPrevious[$player->name] = $controllerService->getTopPostsForPlayer($player, true);
        }
        $topAuthors = $controllerService->getTopAuthors();
        $topAuthorsPrevious = $controllerService->getTopAuthors(true);
        $topPosts = $controllerService->getTopPosts();

        $text = $controllerService->convertToText($topPlayers, $topPostsPerPlayer, $topAuthors, $topPosts);

        $date = date('F Y', strtotime('last month'));

        return view('highlight.highlights')
            ->with('date', $date)
            ->with('top_posts_per_player', $topPostsPerPlayer)
            ->with('top_posts_per_player_previous', $topPostsPerPlayerPrevious)
            ->with('top_players', $topPlayers)
            ->with('top_players_previous', $topPlayersPrevious)
            ->with('top_authors', $topAuthors)
            ->with('top_authors_previous', $topAuthorsPrevious)
            ->with('posts_count', $controllerService->getPostsCount())
            ->with('posts_count_previous', $controllerService->getPostsCount(true))
            ->with('posts_total_score', $controllerService->getPostsTotalScore())
            ->with('posts_total_score_previous', $controllerService->getPostsTotalScore(true))
            ->with('top_posts', $topPosts)
            ->with('unique_players', $controllerService->getUniquePlayersCount())
            ->with('unique_players_previous', $controllerService->getUniquePlayersCount(true))
            ->with('score_per_day', $controllerService->getScorePerDay())
            ->with('score_per_day_previous', $controllerService->getScorePerDay(true))
            ->with('text', $text);
    }
}
