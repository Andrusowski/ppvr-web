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

        $ranking = DB::select("
            SELECT name, ranking.number
            FROM (
                SELECT name, RANK() OVER (ORDER BY score DESC) as number
                FROM authors
            ) as ranking
            WHERE name=:name
        ", ['name' => $name]);

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
            ->with('rank', $ranking[0]->number)
            ->with('posts', $posts)
            ->with('posts_new', $posts_new)
            ->with('author_stats', $author_stats);
    }
}
