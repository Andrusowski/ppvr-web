<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Post;

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
        if(!$post) {
            abort(404);
        }


        $posts_other = Post::where([['map_title', 'LIKE', $post->map_title],
                                    ['map_diff', 'LIKE', $post->map_diff]])
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        $posts_other_new = [];
        if (count($posts_other) >= 10) {
            $posts_other_new = Post::where([['map_title', 'LIKE', $post->map_title],
                                            ['map_diff', 'LIKE', $post->map_diff]])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        $player = Player::find($post->player_id);

        $content = file_get_contents("https://www.reddit.com/r/osugame/comments/" . $post->id . ".json?raw_json=1");
        $post_reddit = json_decode($content);

        $img = '';
        if (isset($post_reddit[0]->data->children[0]->data->preview)) {
            $img = $post_reddit[0]->data->children[0]->data->preview->images[0]->source->url;
        }

        $top_comment = '';
        $top_comment_author = '';
        $top_score = 0;
        $comments = $post_reddit[1]->data->children;

        foreach ($comments as $comment) {
            if (
                isset($comment->data->score)
                && $comment->data->score > $top_score
                && !$comment->data->stickied
                && strlen($comment->data->body) < 500
                && !stripos($comment->data->body_html, 'http')
                && !stripos($comment->data->body_html, 'https')
            ) {
                $top_comment = $comment->data->body_html;
                $top_comment_author = $comment->data->author;
                $top_score = $comment->data->score;
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
