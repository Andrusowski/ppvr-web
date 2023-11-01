<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function getIndex($name)
    {
        $author = Author::whereName($name)->first();
        $author_stats = DB::table('posts')
            ->select(DB::raw('author,
                             (SUM(downs)/SUM(ups))*100 as controversy,
                             SUM(silver) as silver,
                             SUM(gold) as gold,
                             SUM(platinum) as platinum,
                             COUNT(posts.id) as posts'))
            ->where('author', $name)
            ->groupBy('author')
            ->first();

        if (!$author_stats) {
            abort(404);
        }

        $ranking = Author::orderBy('score', 'desc')->get();

        $rank = 1;
        foreach ($ranking as $rankingAuthor) {
            if ($rankingAuthor->author != $author_stats->author) {
                $rank++;
            } else {
                break;
            }
        }

        $posts = DB::table('posts')
            ->select(DB::raw('id,
                              map_artist,
                              map_title,
                              map_diff,
                              score,
                              (downs/ups)*100 as controversy'))
            ->where('author', $name)
            ->orderBy('score', 'desc')
            ->take(10)
            ->get();

        $posts_new = DB::table('posts')
            ->select(DB::raw('id,
                              map_artist,
                              map_title,
                              map_diff,
                              score,
                              (downs/ups)*100 as controversy,
                              created_utc'))
            ->where('author', $name)
            ->orderBy('created_utc', 'desc')
            ->take(10)
            ->get();

        return view('profile.author')
            ->with('author', $author)
            ->with('rank', $rank)
            ->with('posts', $posts)
            ->with('posts_new', $posts_new)
            ->with('author_stats', $author_stats);
    }
}
