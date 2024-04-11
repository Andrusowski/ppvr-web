<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Player;
use Illuminate\Support\Facades\Request;

class SearchController extends Controller
{
    public function postSearch()
    {
        $name = Request::input('name');

        if ($name) {
            $player = Player::where('name', 'LIKE', "$name%")->first();
            $author = Author::where('name', 'LIKE', "$name%")->first();

            if ($player != null || $author != null) {
                return response()->json([
                    'player' => $player ? [
                        'id' => $player->id,
                        'name' => $player->name,
                    ] : null,
                    'author' => $author?->name,
                ]);
            }
        }

        return response()->json(['error' => 'The requested user does not exist or has no Reddit posts yet.']);
    }
}
