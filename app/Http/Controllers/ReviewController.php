<?php

namespace App\Http\Controllers;

use App\Player;
use App\Post;
use App\Tmppost;
use Validator;
use Request;

class ReviewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $tmpposts = Tmppost::orderBy('id','desc')->paginate(50);
        return view('review.index')->with('tmpposts', $tmpposts);
    }

    public function getAdd($id = null, $player = null) {
        $tmppost = Tmppost::find($id);
		if ($tmppost)
		{
            //try to parse the post-title
            $tmppostParsed = array(
                "player" => "",
                "artist" => "",
                "title" => "",
                "diff" => "",
            );

            $matches = array();
            //player
            $match = preg_match("/(.+)\s+[\|丨].+-.+?\[.+?\]/", $tmppost->title, $matches);
            if ($match != FALSE && count($matches) == 2) {
                $tmppostParsed["player"] = $matches[1];
            }
            else {
                $match = preg_match("/(.+)\s*[\|丨].+-.+?\[.+?\]/", $tmppost->title, $matches);
                if ($match != FALSE && count($matches) == 2) {
                    $tmppostParsed["player"] = $matches[1];
                }
            }

            //map data
            $tmpMap = "";
            $match = preg_match("/.+[\|丨]\s+(.+-.+\[.+\])/", $tmppost->title, $matches);
            if ($match != FALSE && count($matches) == 2) {
                $tmpMap = $matches[1];
            }
            else {
                $match = preg_match("/.+[\|丨]\s*(.+-.+\[.+\])/", $tmppost->title, $matches);
                if ($match != FALSE && count($matches) == 2) {
                    $tmpMap = $matches[1];
                }
            }

            //split map data
            $match = preg_match("/(.+)\s-\s(.+?)\s\[(.+?)\]/", $tmpMap, $matches);
            if ($match != FALSE && count($matches) == 4) {
                $tmppostParsed["artist"] = $matches[1];
                $tmppostParsed["title"] = $matches[2];
                $tmppostParsed["diff"] = $matches[3];
            }

            $apiUser;
            if (old('player')) {
                $apiUser = file_get_contents (
                    "https://osu.ppy.sh/api/get_user?k=".env('OSU_API_KEY', 0).
                    "&u=".old('player')."&type=string");
            }
            else {
                $apiUser = file_get_contents (
                    "https://osu.ppy.sh/api/get_user?k=".env('OSU_API_KEY', 0).
                    "&u=".$tmppostParsed["player"]."&type=string");
            }
            $user = json_decode($apiUser);

			return view('review.add')
                ->with('tmppost', $tmppost)
                ->with('tmppostParsed', $tmppostParsed)
                ->with('user', $user);
		}

        return redirect('review');
    }

    public function postAdd($id = null) {
        $validator = Validator::make(Request::all(), Post::$rules);

		if ($validator->fails()) {
			return redirect('review/add/'.$id)->withErrors($validator)->withInput();
		}

        //get all necessary data for a post
        $content = file_get_contents("https://www.reddit.com/r/osugame/comments/".$id.".json");
        $tmppostApi = json_decode($content)[0]->data->children[0]->data;

        $apiUser = file_get_contents (
                   "https://osu.ppy.sh/api/get_user?k=".env('OSU_API_KEY', 0).
                   "&u=".Request::input('player')."&type=string");
        $user = json_decode($apiUser);


        if ($user != null) {
            //check if player exists in Database
            $player = Player::where('name', Request::input('player'))->first();
            if (!$player) {
                $player = new Player();
                $player->id = $user[0]->user_id;
                $player->name = $user[0]->username;
                $player->save();
            }
            //add new post
            $post = new Post();
            $post->id = $tmppostApi->id;
            $post->player_id = $user[0]->user_id;
            $post->map_artist = Request::input('artist');
            $post->map_title = Request::input('title');
            $post->map_diff = Request::input('diff');
            $post->author = $tmppostApi->author;
            $post->score = $tmppostApi->score;
            $post->ups = round($tmppostApi->score * $tmppostApi->upvote_ratio);
            $post->downs = round($tmppostApi->score * (1 - $tmppostApi->upvote_ratio));
            $post->created_utc = $tmppostApi->created_utc;

            if (isset($jsonPost->gildings)) {
                $post->silver = $tmppostApi->gildings->gid_1;
                $post->gold = $tmppostApi->gildings->gid_2;
                $post->platinum = $tmppostApi->gildings->gid_3;
            }
            else {
                $post->gold = $tmppostApi->gilded;
            }

            if ((time() - $tmppostApi->created_utc) > 48*60*60) {
                $post->final = true;
            }
            else {
                $post->final = false;
            }

            $post->save();

            //delete tmppost
            $tmppost = Tmppost::find($id);
    		if ($tmppost)
    		{
    			$tmppost->delete();
    		}
            return redirect('review');
        }
        else {
            return redirect('review/add/'.$id)->withInput();
        }
    }

    public function getDelete($id) {
        $tmppost = Tmppost::find($id);
		if ($tmppost)
		{
			$tmppost->delete();
		}
        return redirect('review');
    }
}
