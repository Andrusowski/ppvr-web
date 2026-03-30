<?php

namespace App\Http\Controllers;

use App\Models\UserGameStats;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameStatsController extends Controller
{
    /**
     * Sync stats from the frontend to the database.
     * This is called when a logged-in user's stats need to be saved.
     */
    public function sync(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $validated = $request->validate([
            'gamesPlayed' => 'required|integer|min:0',
            'totalCorrectRounds' => 'required|integer|min:0',
            'currentStreak' => 'required|integer|min:0',
            'maxStreak' => 'required|integer|min:0',
            'roundBreakdown' => 'required|array|size:11',
            'roundBreakdown.*' => 'integer|min:0',
            'lastPlayedDate' => 'nullable|date_format:Y-m-d',
            'gameRoundResults' => 'nullable|array',
            'gameRoundResults.*' => 'nullable|boolean',
        ]);

        $stats = $user->gameStats;

        if ($stats === null) {
            $stats = new UserGameStats();
            $stats->user_id = $user->id;
        }

        $stats->updateFromFrontendFormat($validated);

        // If game round results are provided, record that the user played today
        if (isset($validated['gameRoundResults'])) {
            $stats->recordGamePlayed($validated['gameRoundResults']);
        }

        $stats->save();

        return response()->json([
            'success' => true,
            'stats' => $stats->toFrontendFormat(),
            'playedToday' => $stats->getTodaysGame(),
        ]);
    }

    /**
     * Get the user's stats from the database.
     */
    public function get(): JsonResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $stats = $user->gameStats;

        return response()->json([
            'stats' => $stats !== null ? $stats->toFrontendFormat() : UserGameStats::getDefaultStats(),
            'playedToday' => $stats !== null ? $stats->getTodaysGame() : null,
        ]);
    }

    /**
     * Handle initial sync on login.
     * - If user is new (no stats in DB), upload local stats
     * - If user has stats in DB, return DB stats to overwrite local
     * - Also returns whether the user has already played today
     */
    public function initialSync(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $localStats = $request->validate([
            'localStats' => 'nullable|array',
            'localStats.gamesPlayed' => 'integer|min:0',
            'localStats.totalCorrectRounds' => 'integer|min:0',
            'localStats.currentStreak' => 'integer|min:0',
            'localStats.maxStreak' => 'integer|min:0',
            'localStats.roundBreakdown' => 'array|size:11',
            'localStats.roundBreakdown.*' => 'integer|min:0',
            'localStats.lastPlayedDate' => 'nullable|date_format:Y-m-d',
        ]);

        $existingStats = $user->gameStats;

        if ($existingStats !== null && $existingStats->games_played > 0) {
            // User has existing stats in DB - return them to overwrite local
            return response()->json([
                'action' => 'use_server',
                'stats' => $existingStats->toFrontendFormat(),
                'playedToday' => $existingStats->getTodaysGame(),
            ]);
        }

        // User has no stats in DB
        if (!empty($localStats['localStats']) && ($localStats['localStats']['gamesPlayed'] ?? 0) > 0) {
            // User has local stats - upload them
            $stats = new UserGameStats();
            $stats->user_id = $user->id;
            $stats->updateFromFrontendFormat($localStats['localStats']);
            $stats->save();

            return response()->json([
                'action' => 'uploaded',
                'stats' => $stats->toFrontendFormat(),
                'playedToday' => null,
            ]);
        }

        // No stats anywhere - return defaults
        return response()->json([
            'action' => 'none',
            'stats' => UserGameStats::getDefaultStats(),
            'playedToday' => null,
        ]);
    }

    /**
     * Delete all user data for the authenticated user.
     * This deletes game stats, the user account, and logs them out.
     * This supports the user's right to erasure under GDPR.
     */
    public function delete(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $stats = $user->gameStats;
        if ($stats !== null) {
            $stats->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Your account and all associated data have been deleted.',
        ]);
    }
}
