<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use App\Models\Post;
use Illuminate\Support\Collection;

class PostControllerService
{
    /**
     * @param Post $post
     *
     * @return Post[]|\Illuminate\Support\Collection
     */
    public function getPostsOther(Post $post): Collection|array
    {
        return Post::where([ [ 'map_title', 'LIKE', $post->map_title ],
            [ 'map_diff', 'LIKE', $post->map_diff ] ])
                   ->orderBy('score', 'desc')
                   ->take(10)
                   ->get();
    }

    /**
     * @param Post $post
     *
     * @return Post[]|\Illuminate\Support\Collection
     */
    public function getPostsOtherNew(Post $post): Collection|array
    {
        return Post::where([ [ 'map_title', 'LIKE', $post->map_title ],
            [ 'map_diff', 'LIKE', $post->map_diff ] ])
                   ->orderBy('created_at', 'desc')
                   ->take(10)
                   ->get();
    }
}
