<?php

namespace App\Http\Controllers;

use App\Services\RedditService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    private const KEY_CACHE_INDEX_PLAYER_RANKING = 'index_player_ranking';
    private const KEY_CACHE_INDEX_AUTHOR_RANKING = 'index_author_ranking';
    private const KEY_CACHE_INDEX_NEW_POSTS = 'index_new_posts';

    public function getIndex()
    {
        $rank_players = 0;
        $posts_players = $this->getPlayerRanking();

        $rank_authors = 0;
        $posts_authors = $this->getAuthorRanking();
        $posts_new = $this->getNewPosts();

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
            ->with('rank_players', $rank_players)
            ->with('posts_players', $posts_players)
            ->with('rank_authors', $rank_authors)
            ->with('posts_authors', $posts_authors)
            ->with('posts_new', $posts_new)
            ->with('top_comment', $topComment ?? null);
    }

    private function getPlayerRanking(): Collection
    {
        return Cache::remember(static::KEY_CACHE_INDEX_PLAYER_RANKING, now()->addMinutes(5), function () {
            return DB::table('posts')
                ->select(DB::raw('posts.player_id,
                    players.name,
                    players.score as score,
                    COUNT(posts.id) as posts'))
                ->join('players', 'posts.player_id', '=', 'players.id')
                ->groupBy('posts.player_id', 'players.name')
                ->orderBy('score', 'desc')
                ->take(5)
                ->get();
        });
    }

    private function getAuthorRanking(): Collection
    {
        return Cache::remember(static::KEY_CACHE_INDEX_AUTHOR_RANKING, now()->addMinutes(5), function () {
            return DB::table('posts')
                ->select(DB::raw('author,
                    authors.score as score,
                    COUNT(posts.id) as posts'))
                ->where('author', '!=', '[deleted]')
                ->join('authors', 'authors.name', '=', 'posts.author')
                ->having(DB::raw(config('ranking.scoreSumQuery')), '>=', 100)
                ->groupBy('author')
                ->orderBy('score', 'desc')
                ->take(5)
                ->get();
        });
    }

    private function getNewPosts(): Collection
    {
        return Cache::remember(static::KEY_CACHE_INDEX_NEW_POSTS, now()->addMinutes(5), function () {
            return DB::table('posts')
                ->select(DB::raw('posts.id,
                    players.name,
                    posts.map_artist,
                    posts.map_title,
                    posts.map_diff,
                    posts.score as score,
                    posts.created_utc'))
                ->join('players', 'posts.player_id', '=', 'players.id')
                ->groupBy('posts.id')
                ->orderBy('posts.created_utc', 'desc')
                ->take(20)
                ->get();
        });
    }
}
