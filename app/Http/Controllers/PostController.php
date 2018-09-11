<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\Player;

class PostController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex($id)
    {
        $post = Post::find($id);
        $posts_other = Post::where([['map_title', 'LIKE', $post->map_title],
                              ['map_diff', 'LIKE', $post->map_diff]])
                           ->orderBy('score', 'desc')
                           ->take(10)
                           ->get();

        $posts_other_new = [];
        if(count($posts_other) >= 10) {
            $posts_other_new = Post::where([['map_title', 'LIKE', $post->map_title],
                                      ['map_diff', 'LIKE', $post->map_diff]])
                                   ->orderBy('created_at', 'desc')
                                   ->take(10)
                                   ->get();
        }

        $player = Player::find($post->player_id);

        $content = file_get_contents("https://www.reddit.com/r/osugame/comments/".$post->id.".json");
        $img = json_decode($content)[0]->data->children[0]->data->url;

        return view('profile.post')->with('post', $post)
                                   ->with('posts_other', $posts_other)
                                   ->with('posts_other_new', $posts_other_new)
                                   ->with('player', $player)
                                   ->with('img', $img);
    }
}
