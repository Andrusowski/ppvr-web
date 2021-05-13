<?php

namespace App\Http\Controllers;

use App\Player;
use Illuminate\Support\Facades\Request;

class SearchController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function postSearch()
    {
        $name = Request::input('name');

        if ($name) {
            $player = Player::where('name', 'LIKE', $name)->first();

            if ($player != null) {
                $player_id = $player->id;

                return redirect('player/' . $player_id);
            }
        }

        return back()->withErrors(['The requested user does not exist or has no Reddit posts yet.']);
    }
}
