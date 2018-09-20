<?php

namespace App\Http\Controllers;

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
        $post_reddit = json_decode($content);

        $img = '';
        if (isset($post_reddit[0]->data->children[0]->data->preview)) {
            $img = $post_reddit[0]->data->children[0]->data->preview->images[0]->source->url;
        }

        $top_comment = '';
        $top_comment_author = '';
        $top_score = 0;
        $comments = $post_reddit[1]->data->children;
        for ($i = 0; $i < count($comments) - 1; $i++) {
            if ($comments[$i]->data->score > $top_score
                && !$comments[$i]->data->stickied
                && strlen($comments[$i]->data->body) < 500
                && !stripos($comments[$i]->data->body_html, 'http')
                && !stripos($comments[$i]->data->body_html, 'https')) {
                $top_comment = $comments[$i]->data->body_html;
                $top_comment_author = $comments[$i]->data->author;
                $top_score = $comments[$i]->data->score;
            }
        }

        return view('profile.post')
            ->with('post', $post)
            ->with('posts_other', $posts_other)
            ->with('posts_other_new', $posts_other_new)
            ->with('player', $player)
            ->with('img', $img)
            ->with('top_comment', $top_comment)
            ->with('top_comment_author', $top_comment_author);
    }
}
