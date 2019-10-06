<?php

namespace App\Console\Commands;

use App\Alias;
use App\Player;
use App\Post;
use App\Tmppost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

        if ($archiveFirstPost) {
            $this->archive();
        }
        else {
            $this->new();
        }
    }

    private function archive() {
        $firstPost = Post::orderBy('created_at', 'DESC')->first();
        $first = 1426668291;
        if ($firstPost != null) {
            $first = $firstPost->created_utc;
        }
        $after = $first;  //time of first scorepost posted
        $month_seconds = 2629743;
        $lastRankSave = 0;

        self::$lastParse = time();

        $bar = $this->output->createProgressBar(time() - 1426668291);

        while ($after < time() - 60*60) { //stop archiving, when posts are younger than an hour
            $jsonContent = file_get_contents('https://api.pushshift.io/reddit/submission/search?subreddit=osugame&sort=asc&limit=100&after='.$after);
            $jsonPosts = json_decode($jsonContent);

            for ($i = 0; $i < sizeof($jsonPosts->data); ++$i) {
                $jsonPost = $jsonPosts->data[$i];
                $this->prepareParse($jsonPost, true);

                $bar->setProgress($jsonPost->created_utc - 1426668291);

                $after = $jsonPost->created_utc;
                if(($jsonPost->created_utc > time() - $month_seconds * 3) && ($jsonPost->created_utc - $lastRankSave > 86400)) {
                    $this->line('Saving ranks...');
                    Artisan::call('parse:ranks '.$jsonPost->created_utc);
                    $lastRankSave = $jsonPost->created_utc;
                }
            }
        }

        $bar->finish();
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
        //check if post already exists
        $tmpPost = Tmppost::where('id', '=', $jsonPost->id)->first();
        $post = Post::where('id', '=', $jsonPost->id)->first();

        $age = time() - $jsonPost->created_utc;

        if ($post === null && $tmpPost === null) {
            //determine if post is final (>48h old)
            if ($age >= 48*60*60) {
                $this->parsePost($jsonPost, true, false);
            }
            else if ($age < 48*60*60) {
                $this->parsePost($jsonPost, false, false);
            }
        }
        //update non-final post, if it already exists in the database (only in non-archive mode)
        else if (!$archive){
            if ($post->final == 0) {
                $content = file_get_contents('https://www.reddit.com/r/osugame/comments/'.$jsonPost->id.'.json');
                $jsonPostDetail = json_decode($content)[0]->data->children[0]->data;

                if ($age >= 24*60*60) {
                    $this->updatePost($jsonPostDetail, 1);
                }
                else {
                    $this->updatePost($jsonPostDetail, 0);
                }
            }
        }
    }

    private function parsePost($jsonPost, $final, $archive) {
        /* check for characteristic characters from the already established format
        Player Name | Song Artist - Song Title [Diff Name] +Mods */
        $postTitle = $jsonPost->title;
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
            $match = preg_match("/(?:\(.+?\)\s)*(.+?)\s*(?:\(.+\))*\s*[\|丨].+-.+?\[.+?\]/", $postTitle, $matches);
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
                $this->line($jsonPost->permalink);
                $this->line("Original: ".$jsonPost->title); //Debug
                //check some additional stuff before marking as final
                $this->line('Parsed: '.$playerName.' | '.
                    $parsedPost->map_artist. ' - '.
                    $parsedPost->map_title. ' [' .
                    $parsedPost->map_diff."]");
                //take a break to prevent osu!api spam
                while (self::$lastParse == time()) {
                    //wait
                    usleep(100000);
                }
                self::$lastParse = time();
                $content = file_get_contents('https://www.reddit.com/r/osugame/comments/'.$jsonPost->id.'.json');
                $jsonPostDetail = json_decode($content)[0]->data->children[0]->data;
                $this->preparePost($jsonPostDetail, $parsedPost, $playerName, $final);
                return true;
            }
            else {
                $this->addTmpPost($jsonPost);
            }
        }
    }

    private function preparePost($jsonPost, $parsedPost, $playerName, $final) {
        $apiUser = file_get_contents (
            "https://osu.ppy.sh/api/get_user?k=".env('OSU_API_KEY').
            "&u=".urlencode($playerName)."&type=string"
        );
        $user = json_decode($apiUser);

        //if the api can find the username, check if its in the DB and insert
        if (isset($user[0]->user_id)) {
            $dbUser = Player::where('id', '=', $user[0]->user_id)->first();
            if ($dbUser === null) {
                $this->addPlayer($user[0]);
                $dbUser = Player::where('id', '=', $user[0]->user_id)->first();
            }

            $this->updatePlayer($user[0], $dbUser);
            $this->addPost($jsonPost, $parsedPost, $user, $final);
        }
        /* else check if the username exists in the DB as an alias and retry
        the osu!api call using the alias */
        else {
            $alias = Alias::where('alias', '=', $playerName)->orderBy('created_at', 'DESC')->first();
            if ($alias != null) {
                $apiUserAlias = file_get_contents (
                    "https://osu.ppy.sh/api/get_user?k=".env('OSU_API_KEY').
                    "&u=".urlencode($alias->alias)."&type=string"
                );

                $userAlias = json_decode($apiUserAlias);
                if ($userAlias != null)
                {
                    $this->addPost($jsonPost, $parsedPost, $userAlias, $final);
                }
            }
            else {
                $this->addTmpPost($jsonPost);
                $this->error('tmp');
            }
        }
    }

    private function addPlayer($user){
        $newPlayer = new Player();
        $newPlayer->id = $user->user_id;
        $newPlayer->name = $user->username;
        $newPlayer->save();
    }

    private function updatePlayer($user, $dbUser) {
        if ($dbUser->name !=  $user->username
            && $dbUser->name != ''
            && $dbUser->name != null
        ) {
            $alias = new Alias();
            $alias->player_id = $user->user_id;
            $alias->alias = $dbUser->name;
            $alias->save();

            $dbUser->name = $user->username;
            if($dbUser->save()) {
                $this->info("Player \"".$alias->alias."\" updated successfully! New name:".$dbUser->name);
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
        $post->final = $final;
        $post->created_utc = $jsonPost->created_utc;

        //post platin and silver update
        if (isset($jsonPost->gildings)) {
            if (isset($jsonPost->gildings->gid_1)){
                $post->silver = $jsonPost->gildings->gid_1;
            }
            else {
                $post->silver = 0;
            }

            if (isset($jsonPost->gildings->gid_2)){
                $post->gold = $jsonPost->gildings->gid_2;
            }
            else {
                $post->gold = 0;
            }

            if (isset($jsonPost->gildings->gid_3)){
                $post->platinum = $jsonPost->gildings->gid_3;
            }
            else {
                $post->platinum = 0;
            }
        }
        //pre
        else {
            $post->gold = $jsonPost->gilded;
        }

        if($post->save()) {
            $this->info('added');
            $this->updatePlayerScore($post->player_id);
        }
    }

    private function addTmpPost($jsonPost) {
        $post = new Tmppost();

        $post->id = $jsonPost->id;
        $post->title = $jsonPost->title;
        $post->author = $jsonPost->author;

        //$ups = round($jsonPost->score * $jsonPost->upvote_ratio);
        //$downs = round($jsonPost->score * (1 - $jsonPost->upvote_ratio));
        $post->score = $jsonPost->score;

        $post->save();
    }

    private function updatePost($jsonPost, $final) {
        $post = Post::where('id', '=', $jsonPost->id)->first();
        $scorePre = $post->score;

        $post->ups = round($jsonPost->score * $jsonPost->upvote_ratio);
        $post->downs = round($jsonPost->score * (1 - $jsonPost->upvote_ratio));
        $post->score = $post->ups - $post->downs;

        // update if needed
        if($scorePre != $post->score) {
            $post->final = $final;

            //post platin and silver update
            if (isset($jsonPost->gildings)) {
                if (isset($jsonPost->gildings->gid_1)){
                    $post->silver = $jsonPost->gildings->gid_1;
                }
                else {
                    $post->silver = 0;
                }

                if (isset($jsonPost->gildings->gid_2)){
                    $post->gold = $jsonPost->gildings->gid_2;
                }
                else {
                    $post->gold = 0;
                }

                if (isset($jsonPost->gildings->gid_3)){
                    $post->platinum = $jsonPost->gildings->gid_3;
                }
                else {
                    $post->platinum = 0;
                }
            }
            //pre
            else {
                $post->gold = $jsonPost->gilded;
            }

            if($post->save()) {
                $this->line('Updated: '.
                    $post->map_artist. ' - '.
                    $post->map_title. ' [' .
                    $post->map_diff."] ".
                    'Score: '.$scorePre.' -> '.$post->score);
            }
        }
    }

    private function updatePlayerScore($playerId) {
        $this->line($playerId);
        $this->line(DB::statement('
            UPDATE players
            JOIN (
                SELECT player_id, SUM(round((score+(platinum*180)+(gold*50)+(silver*10)) * POWER(0.95, row_num - 1))) AS weighted
                FROM (
                    SELECT row_number() over (partition BY player_id ORDER BY score DESC) row_num, score, silver, gold, platinum, player_id
                    FROM posts
                    ORDER BY score DESC
                ) AS ranking
                GROUP BY player_id
                ORDER BY weighted DESC
            ) weighted ON players.id=weighted.player_id
            SET score=weighted.weighted
            WHERE players.id='.$playerId
        ));
    }
}
