<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Services\Controller\PlayerControllerService;

class PlayerController extends Controller
{
    public function getIndex($id)
    {
        $controller = new PlayerControllerService();

        $player = Player::find($id);
        if (!$player) {
            abort(404);
        }

        $ranking = $controller->getRanking();
        $rank = 1;
        foreach ($ranking as $rankingPlayer) {
            if ($rankingPlayer->id != $player->id) {
                $rank++;
            } else {
                break;
            }
        }

        return view('profile.player')
            ->with('rank', $rank)
            ->with('posts', $controller->getPosts($id))
            ->with('player', $player)
            ->with('alias', $controller->getLatestAlias($player))
            ->with('posts_new', $controller->getNewPosts($id))
            ->with('player_stats', $controller->getPlayerStats($id))
            ->with('ranks', $controller->getRanks($player));
    }
}
