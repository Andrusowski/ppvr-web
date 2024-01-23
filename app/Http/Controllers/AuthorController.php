<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Services\Controller\AuthorControllerService;

class AuthorController extends Controller
{
    public function getIndex($name)
    {
        $controllerService = new AuthorControllerService();

        $author_stats = $controllerService->getAuthorStats($name);

        if (!$author_stats) {
            abort(404);
        }

        return view('profile.author')
            ->with('author', Author::whereName($name)->first())
            ->with('rank', $controllerService->getRanking($name)[0]->number)
            ->with('posts', $controllerService->getPosts($name))
            ->with('posts_new', $controllerService->getNewPosts($name))
            ->with('author_stats', $author_stats);
    }
}
