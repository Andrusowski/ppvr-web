<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGameStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OsuAuthController extends Controller
{
    /**
     * Redirect to osu! OAuth authorization page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('osu')->redirect();
    }

    /**
     * Handle callback from osu! OAuth.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $osuUser = Socialite::driver('osu')->user();
        } catch (\Exception $e) {
            return redirect('/game')->with('error', 'Failed to authenticate with osu!');
        }

        $user = User::where('osu_id', $osuUser->getId())->first();

        if ($user === null) {
            $user = User::create([
                'osu_id' => $osuUser->getId(),
                'name' => $osuUser->getName(),
                'avatar_url' => $osuUser->getAvatar(),
                'access_token' => $osuUser->token,
                'refresh_token' => $osuUser->refreshToken,
                'token_expires_at' => now()->addSeconds($osuUser->expiresIn),
            ]);
        } else {
            $user->update([
                'name' => $osuUser->getName(),
                'avatar_url' => $osuUser->getAvatar(),
                'access_token' => $osuUser->token,
                'refresh_token' => $osuUser->refreshToken,
                'token_expires_at' => now()->addSeconds($osuUser->expiresIn),
            ]);
        }

        Auth::login($user, true);

        // Mark that sync should happen on frontend
        session()->flash('osu_auth_success', true);
        session()->flash('is_new_user', $user->wasRecentlyCreated);

        return redirect('/game');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/game');
    }

    /**
     * Get current authenticated user data for the frontend.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();

        if ($user === null) {
            return response()->json([
                'authenticated' => false,
            ]);
        }

        $stats = $user->gameStats;

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'osu_id' => $user->osu_id,
                'name' => $user->name,
                'avatar_url' => $user->avatar_url,
            ],
            'stats' => $stats !== null ? $stats->toFrontendFormat() : UserGameStats::getDefaultStats(),
            'playedToday' => $stats !== null ? $stats->getTodaysGame() : null,
        ]);
    }
}
