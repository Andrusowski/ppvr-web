<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;

class AuthorController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex($name)
    {
        $author_stats = DB::table('posts')->select(DB::raw('author,
                                                            (SUM(downs)/SUM(ups))*100 as controversy,
                                                            SUM(score*(1+((gilded)*0.1))) as score,
                                                            AVG(score*(1+((gilded)*0.1))) as score_avg,
                                                            COUNT(posts.id) as posts'))
                                   ->where('author', $name)
                                   ->groupBy('author')
                                   ->first();

        $posts = DB::table('posts')->select(DB::raw('id,
                                                     map_artist,
                                                     map_title,
                                                     map_diff,
                                                     score*(1+((gilded)*0.1)) as score,
                                                     (downs/ups)*100 as controversy'))
                                   ->where('author', $name)
                                   ->orderBy('score', 'desc')
                                   ->take(10)
                                   ->get();

       $posts_new = [];
       if(count($posts) >= 10) {
           $posts_new = DB::table('posts')->select(DB::raw('id,
                                                            map_artist,
                                                            map_title,
                                                            map_diff,
                                                            score*(1+((gilded)*0.1)) as score,
                                                            (downs/ups)*100 as controversy,
                                                            created_utc'))
                                          ->where('author', $name)
                                          ->orderBy('created_utc', 'desc')
                                          ->take(10)
                                          ->get();
       }

        return view('profile.author')->with('posts', $posts)
                                     ->with('posts_new', $posts_new)
                                     ->with('author_stats', $author_stats);
    }
}
