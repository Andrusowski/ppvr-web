<?php

namespace App\Console\Commands;

use App\Player;
use App\Post;
use App\Tmppost;
use Illuminate\Console\Command;

class ParseRedditPosts extends Command
{
    /**
     * @var int
     */
    private static $lastParse;

    /**
     * consts taken from christopher-dG's osu-bot
     */
    private static $title_ignores = [
        'UNNOTICED',
        'UNNOTICED?',
        'RIPPLE',
        'GATARI',
        'UNSUBMITTED',
        'OFFLINE',
        'RESTRICTED',
        'BANNED',
        'UNRANKED',
        'LOVED',
        'STANDARD',
        'STD',
        'OSU!',
        'O!STD',
        'OSU!STD',
        'OSU!STANDARD',
        'TAIKO',
        'OSU!TAIKO',
        'O!TAIKO',
        'CTB',
        'O!CATCH',
        'OSU!CATCH',
        'CATCH',
        'OSU!CTB',
        'O!CTB',
        'MANIA',
        'O!MANIA',
        'OSU!MANIA',
        'OSU!M',
        'O!M',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:reddit {--archive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse new Reddit scoreposts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $archiveFirstPost = $this->option('archive');

        if (isset($archiveFirstPost)) {
            $this->archive();
        }
        else {
            $this->new();
        }
    }

    private function archive() {
        $after = 1426668291;  //time of first scorepost posted
        self::$lastParse = time();

        while ($after < time() - 60*60) { //stop archiving, when posts are younger than an hour
            $jsonContent = file_get_contents('https://api.pushshift.io/reddit/submission/search?subreddit=osugame&sort=asc&limit=100&after='.$after);
            $jsonPosts = json_decode($jsonContent);

            for ($i = 0; $i < sizeof($jsonPosts->data); ++$i) {
                $jsonPost = $jsonPosts->data[$i];
                $this->prepareParse($jsonPost, true);
            }
        }
    }

    private function new() {
        $jsonContent=file_get_contents('https://ga.reddit.com/r/osugame/search.json?q=flair%3AGameplay&sort=new&restrict_sr=on&t=all');
        $jsonPosts = json_decode($jsonContent);

        for ($i = 0; $i < $jsonPosts->data->dist; ++$i) {
            $jsonPost = $jsonPosts->data->children[$i]->data;
            $this->prepareParse($jsonPost, false);
        }
    }

    private function prepareParse($jsonPost, $archive) {
        $this->line($jsonPost->title);

        //check if post already exists
        $tmpPost = Tmppost::where('id', '==', $jsonPost->id)->first();
        $post = Post::where('id', '==', $jsonPost->id)->first();

        $age = time() - $jsonPost->created_utc;

        if ($post === null && $tmpPost === null) {
            //determine if post is final (>48h old)
            if ($age >= 48*60*60) {
                $this->parsePost($jsonPost, true, false);
            }
            else if ($age < 48*60*60) {
                $this->parsePost($jsonPost, true, false);
            }
        }
        //update non-final post, if it already exists in the database (only in non-archive mode)
        else if (!$archive){
            if ($post->final == 0) {
                if ($age >= 24*60*60) {
                    $this->updatePost($jsonPost, 1);
                }
                else {
                    $this->updatePost($jsonPost, 0);
                }
            }
        }
    }

    private function parsePost($post, $final, $archive) {
        /* check for characteristic characters from the already established format
        Player Name | Song Artist - Song Title [Diff Name] +Mods */
        $postTitle = $post->title;
        if (preg_match('/.*\|.*\-.*\[.*\].*/', $postTitle))
        {
            //clean up posttitle from various annotations
            $postTitle = preg_replace('/([\[\(]\#.*[\]\)])/U', '', $postTitle);
            foreach(self::$title_ignores as $ignore) {
                $postTitle = preg_replace("/([\[\(]\Q".$ignore."\E[\]\)])/i", '', $postTitle);
            }
            //parse relevant information from post title
            $playerName = '**error**';
            $parsedPost = new Post();
            $parsedPost->map_artist = '**error**';
            $parsedPost->map_title = '**error**';
            $parsedPost->map_diff = '**error**';

            $parseError = false;
            $matches = array();
            //Player
            $match = preg_match("/(.+)\s*[\|丨].+-.+?\[.+?\]/", $postTitle, $matches);
            if ($match != FALSE && count($matches) == 2) {
                $playerName = trim($matches[1]);
            }
            else {
                $parseError = true;
            }
            //map Data
            $tmpMap = '';
            $match = preg_match("/.+[\|丨]\s*(.+-.+\[.+\])/", $postTitle, $matches);
            if ($match != FALSE && count($matches) == 2) {
                $tmpMap = $matches[1];
            }
            else {
                $parseError = true;
            }
            //split map Data
            $match = preg_match("/(.+)\s-\s(.+?)\s\[(.+?)\]/", $tmpMap, $matches);
            if ($match != FALSE && count($matches) == 4) {
                $parsedPost->map_artist = htmlspecialchars_decode(trim($matches[1]));
                $parsedPost->map_title = htmlspecialchars_decode(trim($matches[2]));
                $parsedPost->map_diff = htmlspecialchars_decode(trim($matches[3]));
            }
            else {
                $parseError = true;
            }
            if ($parseError == false) {
                echo("\nOriginal: ".$post->title."\n"); //Debug
                //check some additional stuff before marking as final
                echo('Parsed: '.$playerName.' | '.
                    $parsedPost->map_artist. ' - '.
                    $parsedPost->map_title. ' [' .
                    $parsedPost->map_diff."]\n");
                //take a break to prevent osu!api spam
                while (self::$lastParse == time()) {
                    //wait
                }
                self::$lastParse = time();
                $content = file_get_contents('https://www.reddit.com/r/osugame/comments/'.$post->id.'.json');
                $jsonPost = json_decode($content)[0]->data->children[0]->data;
                $this->preparePost($jsonPost, $parsedPost, $final);
                return true;
            }
            else {
                Database::insertNewPostTmp($post, $parsedPost);
            }
        }
    }

    private function preparePost($jsonPost, $parsedPost, $final) {
        $apiUser = file_get_contents (
            "https://osu.ppy.sh/api/get_user?k=".env('OSU_API_KEY').
            "&u=".$parsedPost["player"]."&type=string"
        );
        $user = json_decode($apiUser);

        //if the api can find the username, check if its in the DB and insert
        if ($user != null) {
            $dbUser = Player::where('id', '==', $user[0]->user_id)->first();
            if ($dbUser->num_rows == 0) {
                $this->addPlayer($user, $dbUser);
            }
            if ($dbUser->num_rows <= 1) {
                $this->updatePlayer($user);
                $this->addPost($jsonPost, $parsedPost, $user, $final);
            }
            else {
                $this->error('Somehow the user "'.$user[0]->user_id.'" exists multiple times?!');
            }
        }
        /* else check if the username exists in the DB as an alias and retry
        the osu!api call using the alias */
        else {
            $dbUserAlias = "SELECT id, name, alias
            FROM ppvr.players
            WHERE alias='".$parsedPost["player"]."';";
            $resultAlias = $db->query($dbUserAlias);
            if ($resultAlias->num_rows == 1) {
                $resultUserAlias = $resultAlias->fetch_assoc();
                $apiUserAlias = file_get_contents (
                    "https://osu.ppy.sh/api/get_user?k=".API_KEY.
                    "&u=".$resultUserAlias["name"]."&type=string"
                );
                $userAlias = json_decode($apiUserAlias);
                if ($userAlias != null)
                {
                    Database::insertNewPost($post, $parsedPost, $userAlias, $resultAlias, $final);
                }
            }
            else {
                Database::insertNewPostTmp($post, $parsedPost);
            }
        }
    }

    private function addPlayer($user) {
        $newPlayer = new Player();
        $newPlayer->id = $user[0]->user_id;
        $newPlayer->name = $user[0]->username;
        $newPlayer->save();
    }

    private function updatePlayer($user, $dbUser) {
        if ($dbUser->name !=  $user[0]->username
            && $dbUser->name != ''
            && $dbUser->name != null
        ) {
            $dbUser->alias = $dbUser->name;
            $dbUser->name = $user[0]->username;
            if($dbUser->save()) {
                $this->info("Player \"".$dbUser->alias."\" updated successfully! New name:".$dbUser->name);
            }
        }
    }

    private function addPost($jsonPost, $parsedPost, $user, $final) {
        $post = $parsedPost;
        $post->id = $jsonPost->id;
        $post->player_id = $user[0]->user_id;
        $post->author = $jsonPost->author;
        $post->ups = round($jsonPost->score * $jsonPost->upvote_ratio);
        $post->downs = round($jsonPost->score * (1 - $jsonPost->upvote_ratio));
        $post->score = $post->ups - $post->downs;
        $post->silver = $jsonPost->gildings->gid_1;
        $post->gold = $jsonPost->gildings->gid_2;
        $post->platinum = $jsonPost->gildings->gid_3;
        $post->final = $final;
        $post->created_utc = $jsonPost->created_utc;

        $post->save();
    }

    private function updatePost($post, $final) {

    }
}
