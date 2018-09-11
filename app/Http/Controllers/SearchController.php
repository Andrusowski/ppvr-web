<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Support\Facades\DB;
use App\Player;

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
            $player_id = Player::where('name', 'LIKE', $name)->first()->id;

            return redirect('player/'.$player_id);
        }
        else {
            return back();
        }
    }
}
