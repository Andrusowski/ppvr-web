<?php

namespace App\Http\Controllers;

use App\Services\Controller\IndexControllerService;
use App\Services\RedditService;

class IndexController extends Controller
{
    public function getIndex()
    {
        $controllerService = new IndexControllerService();

        $posts_new = $controllerService->getNewPosts();

        //find top new post
        $posts_new_top = 0;
        $posts_new_top_score = 0;
        $postsCount = count($posts_new);
        for ($i = 0; $i < $postsCount; $i++) {
            if ($posts_new[$i]->score > $posts_new_top_score) {
                $posts_new_top = $i;
                $posts_new_top_score = $posts_new[$i]->score;
            }
        }

        if ($posts_new_top_score >= 100) {
            //get top comment from top post
            $topComment = RedditService::getTopCommentForPost($posts_new[$posts_new_top]->id);
        }

        return view('index')
            ->with('rank_players', 0)
            ->with('posts_players', $controllerService->getPlayerRanking())
            ->with('rank_authors', 0)
            ->with('posts_authors', $controllerService->getAuthorRanking())
            ->with('posts_new', $posts_new)
            ->with('top_comment', $topComment ?? null);
    }
}
