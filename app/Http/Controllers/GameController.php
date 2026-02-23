<?php

namespace App\Http\Controllers;

use App\Services\Controller\GameControllerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display the game page.
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $controllerService = new GameControllerService();
        $game = $controllerService->getDailyGame();

        if (!$game) {
            return view('game.index')
                ->with('gameData', null)
                ->with('noGame', true);
        }

        $gameData = $controllerService->getGameData($game);

        return view('game.index')
            ->with('gameData', $gameData)
            ->with('noGame', false);
    }

    /**
     * Get the daily game data as JSON (for Vue component).
     *
     * @return JsonResponse
     */
    public function getGameData(): JsonResponse
    {
        $controllerService = new GameControllerService();
        $game = $controllerService->getDailyGame();

        if (!$game) {
            return response()->json([
                'error' => 'No game available',
                'message' => 'Today\'s game has not been created yet. Please try again later.',
            ], 404);
        }

        $gameData = $controllerService->getGameData($game);

        return response()->json($gameData);
    }

    /**
     * Validate a player's choice for a round.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function validateChoice(Request $request): JsonResponse
    {
        $round = (int)$request->input('round');
        $chosenPostId = $request->input('chosen_post_id');

        if (!$round || !$chosenPostId) {
            return response()->json([
                'valid' => false,
                'error' => 'Missing required parameters',
            ], 400);
        }

        $controllerService = new GameControllerService();
        $game = $controllerService->getDailyGame();

        if (!$game) {
            return response()->json([
                'valid' => false,
                'error' => 'No game available',
            ], 404);
        }

        $result = $controllerService->validateChoice($game, $round, $chosenPostId);

        return response()->json($result);
    }
}
