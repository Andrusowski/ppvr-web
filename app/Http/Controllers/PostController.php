<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Post;

class PostController extends Controller
{
    public function getIndex($id)
    {
        $post = Post::find($id);
        if (!$post) {
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

        $img = '';
        /** DISABLED UNTIL PROPER CACHING OLUTION IS FOUND
        $top_comment = '';
        $top_comment_author = '';
        $top_score = 0;

        try {
            $post_reddit = (new RedditClient())->getComments($post->id);
        } catch (ErrorException $e) {
            $post_reddit = null;
        }

        if ($post_reddit) {
            if (isset($post_reddit[0]->data->children[0]->data->preview)) {
                $img = $post_reddit[0]->data->children[0]->data->preview->images[0]->source->url;
            }

            $comments = $post_reddit[1]->data->children;

            foreach ($comments as $comment) {
                if (
                    isset($comment->data->score)
                    && $comment->data->score > $top_score
                    && !$comment->data->stickied
                    && strlen($comment->data->body) < 500
                    && !stripos($comment->data->body_html, 'http')
                    && !stripos($comment->data->body_html, 'https')
                    && !stripos($comment->data->body_html, '[deleted]')
                ) {
                    $top_comment = $comment->data->body_html;
                    $top_comment_author = $comment->data->author;
                    $top_score = $comment->data->score;
                }
            }
        }
        **/

        return view('profile.post')
            ->with('post', $post)
            ->with('posts_other', $posts_other)
            ->with('posts_other_new', $posts_other_new)
            ->with('player', $player)
            ->with('img', $img);

            //->with('top_comment', $top_comment)
            //->with('top_comment_author', $top_comment_author);
    }
}
