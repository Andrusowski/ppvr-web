<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tmppost;
use App\Services\Controller\StatsControllerService;

class StatsController extends Controller
{
    # Simple HTML page for now. Move changelog into the DB later when it gets huge.
    public function getIndex()
    {
        $controllerService = new StatsControllerService();

        return view('content.stats')
            ->with('postsTotal', Post::count())
            ->with('tmpPostsTotal', Tmppost::count())
            ->with('postsHistory', $controllerService->getPostsHistory())
            ->with('upvotesHistory', $controllerService->getUpvotesHistory());
    }
}
