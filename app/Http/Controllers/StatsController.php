<?php

namespace App\Http\Controllers;

use App\Post;
use App\Tmppost;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    # Simple HTML page for now. Move changelog into the DB later when it gets huge.
    public function getIndex()
    {
        $postsTotal = Post::count();
        $tmpPostsTotal = Tmppost::count();

        $postsHistory = DB::table('posts')
            ->select(DB::raw('COUNT(id) as postsDaily'), DB::raw('DATE(created_at) as date'))
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->limit(90)
            ->get();

        $upvotesHistory = DB::table('posts')
            ->select(DB::raw('SUM(score) as postsDaily'), DB::raw('DATE(created_at) as date'))
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->limit(90)
            ->get();

        return view('content.stats')
            ->with('postsTotal', $postsTotal)
            ->with('tmpPostsTotal', $tmpPostsTotal)
            ->with('postsHistory', $postsHistory)
            ->with('upvotesHistory', $upvotesHistory);
    }
}
