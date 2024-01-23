<?php
namespace App\Http\Controllers;

use App\Services\Controller\RankingControllerService;

class RankingController extends Controller
{
    public function getIndexPlayer($sort = 'score')
    {
        $controllerService = new RankingControllerService();

        $posts = $controllerService->getPlayerPosts($sort);
        $rank = RankingControllerService::ENTRIES_PER_PAGE * ($posts->currentPage() - 1);

        return view('ranking.player')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort)
            ->with('currentPage', $posts->currentPage())
            ->with('pageAmount', $posts->total())
            ->with('pageUrls', $controllerService->getPageUrls($posts));
    }

    public function getIndexAuthor($sort = 'score')
    {
        $controllerService = new RankingControllerService();

        $posts = $controllerService->getAuthorPosts($sort);
        $rank = RankingControllerService::ENTRIES_PER_PAGE * ($posts->currentPage() - 1);

        return view('ranking.author')
            ->with('posts', $posts)
            ->with('rank', $rank)
            ->with('sort', $sort)
            ->with('currentPage', $posts->currentPage())
            ->with('pageAmount', $posts->total())
            ->with('pageUrls', $controllerService->getPageUrls($posts));
    }
}
