<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Http\Controllers;

use App\Services\Controller\HighlightsControllerService;

class HighlightsController
{
    public function getIndex()
    {
        date_default_timezone_set('Etc/UCT');
        $controllerService = new HighlightsControllerService();

        $top_players = $controllerService->getTopPlayers();
        $top_posts_per_player = [];
        foreach ($top_players as $player) {
            $top_posts_per_player[$player->name] = $controllerService->getTopPostsForPlayer($player);
        }
        $top_authors = $controllerService->getTopAuthors();
        $top_posts = $controllerService->getTopPosts();

        $text = $controllerService->convertToText($top_players, $top_posts_per_player, $top_authors, $top_posts);

        $date = date('F Y', strtotime('last month'));

        return view('highlight.highlights')
            ->with('date', $date)
            ->with('top_posts_per_player', $top_posts_per_player)
            ->with('top_players', $top_players)
            ->with('top_authors', $top_authors)
            ->with('posts_count', $controllerService->getPostsCount())
            ->with('posts_total_score', $controllerService->getPostsTotalScore())
            ->with('top_posts', $top_posts)
            ->with('unique_players', $controllerService->getUniquePlayersCount())
            ->with('score_per_day', $controllerService->getScorePerDay())
            ->with('text', $text);
    }
}
