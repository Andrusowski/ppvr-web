<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services;

use App\Models\Cache\Comment;
use App\Services\Clients\RedditClient;
use Illuminate\Support\Facades\Cache;
use Throwable;

class RedditService
{
    private const KEY_CACHED_TOP_COMMENT = 'cached_top_comment';

    public static function getTopCommentForPost(string $postId, bool $disableCache = false): ?Comment
    {
        if ($disableCache) {
            return static::fetchTopComment($postId);
        }

        return Cache::remember(static::KEY_CACHED_TOP_COMMENT, now()->addMinutes(10), function () use ($postId) {
            return static::fetchTopComment($postId);
        });
    }

    private static function fetchTopComment(string $postId): ?Comment
    {
        $redditClient = new RedditClient();
        try {
            $post_reddit = $redditClient->getComments($postId, $redditClient->getAccessToken());
        } catch (Throwable $e) {
            // ignore, most likely too many requests for now
            $post_reddit = null;
        }

        $topComment = new Comment();
        if ($post_reddit) {
            $comments = $post_reddit[1]->data->children;
            foreach ($comments as $comment) {
                if (
                    isset($comment->data->score)
                    && $comment->data->score > $topComment->getScore()
                    && !$comment->data->stickied
                    && strlen($comment->data->body) < 500
                    && !stripos($comment->data->body_html, 'http')
                    && !stripos($comment->data->body_html, 'https')
                ) {
                    $topComment->setBody($comment->data->body_html);
                    $topComment->setAuthor($comment->data->author);
                    $topComment->setLink('https://www.reddit.com' . $comment->data->permalink);
                    $topComment->setScore($comment->data->score);

                    return $topComment;
                }
            }
        }

        return null;
    }
}
