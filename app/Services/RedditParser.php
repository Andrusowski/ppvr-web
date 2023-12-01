<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services;

use App\Exceptions\PostNotFoundException;
use App\Models\Alias;
use App\Models\Api\User;
use App\Models\Player;
use App\Models\Post;
use App\Models\Tmppost;
use App\Services\Clients\OsuClient;
use App\Services\Clients\RedditClient;
use Carbon\Carbon;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;
use function Sentry\captureException;

class RedditParser
{
    private const TIMESTAMP_FIRST_SCOREPOST = 1426668291; // Timestamp of the first valid scorepost
    private const TIME_MONTH = 2629743;
    private const TIME_DAY = 86400;

    private const REGEX_SCOREPOST = '/.*\|.*\-.*\[.*\].*/';
    private const REGEX_POST_TITLE = '/([\[\(]\#.*[\]\)])/U';
    private const REGEX_POST_PLAYER_NAME = '/(?:\(.+?\)\s)*(.+?)\s*(?:\(.+\))*\s*[\|丨].+-.+?\[.+?\]/u';
    private const REGEX_POST_MAP_DATA = '/.+[\|丨]\s*(.+-.+\[.+\])/u';
    private const REGEX_POST_MAP_DATA_GROUPS = '/(.+)\s-\s(.+?)\s\[(.+?)\]/';

    /**
     * consts taken from christopher-dG's osu-bot
     */
    private const TITLE_IGNORES = [
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
     * @var float|string
     */
    private static $lastParse;
    /**
     * @var RedditClient
     */
    private $redditClient;

    /**
     * @var OsuClient
     */
    private $osuClient;

    /**
     * @var OutputStyle
     */
    private $output;

    /**
     * @var ProgressBar
     */
    private $bar;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->output = new OutputStyle(new ArgvInput(), new ConsoleOutput());
        $this->redditClient = new RedditClient();
        $this->osuClient = new OsuClient();

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% -- %message%');
    }

    public function archive()
    {
        $firstPost = Post::orderBy('created_at', 'DESC')->first();
        $after = $firstPost->created_utc ?? self::TIMESTAMP_FIRST_SCOREPOST;
        $lastRankSave = 0;

        self::$lastParse = microtime(true);

        $this->bar = $this->output->createProgressBar(time() - self::TIMESTAMP_FIRST_SCOREPOST);
        $this->bar->setFormat('custom');
        $this->bar->setMessage('Reading from archive...');

        while ($after < time() - 60 * 60) { //stop archiving, when posts are younger than an hour
            $jsonPosts = $this->redditClient->getArchiveAfter($after);

            foreach ($jsonPosts->data as $jsonPost) {
                try {
                    $this->prepareParse($jsonPost, true);
                } catch (Throwable $exception) {
                    captureException($exception);
                }

                $this->bar->setProgress($jsonPost->created_utc - self::TIMESTAMP_FIRST_SCOREPOST);

                $after = $jsonPost->created_utc;
                if (
                    (($jsonPost->created_utc - $lastRankSave) > self::TIME_DAY) &&
                    ($jsonPost->created_utc > (time() - self::TIME_MONTH * 3))
                ) {
                    $this->output->writeln('Saving ranks...');
                    Artisan::call('parse:ranks ' . $jsonPost->created_utc);
                    $lastRankSave = $jsonPost->created_utc;
                }
            }
        }

        $this->bar->finish();
    }

    public function new()
    {
        $accessToken = $this->redditClient->getAccessToken();
        $jsonPosts = $this->redditClient->getNewPosts($accessToken);

        $latestPostPreAdd = Post::orderByDesc(Post::CREATED_AT)->first();
        $newPostIds = [];

        foreach ($jsonPosts->data->children as $jsonPost) {
            $postId = $jsonPost->data->id;
            if (!Post::where('id', '=', $postId)->exists()) {
                $newPostIds[] = $postId;
            }
        }

        if ($latestPostPreAdd) {
            $existingPostIds = Post::whereBetween(Post::CREATED_AT, [new Carbon('-2 days'), $latestPostPreAdd->created_at])->pluck('id')->toArray();
            $postsIdsToUpdate = array_merge($newPostIds, $existingPostIds);

            $this->bar = $this->output->createProgressBar(count($postsIdsToUpdate));
            $this->bar->setFormat('custom');
            $this->bar->setMessage('Updating existing posts...');

            foreach ($postsIdsToUpdate as $postsId) {
                $apiPostData = (new RedditClient())->getComments($postsId, $accessToken);
                $this->prepareParse($apiPostData, false);
                $this->bar->advance();
            }
        }
    }

    public function updateFromTop(int $minScore = 0)
    {
        $accessToken = $this->redditClient->getAccessToken();

        $existingPostIds = Post::orderByDesc('score')->where('score', '>', $minScore)->pluck('id')->toArray();

        $this->bar = $this->output->createProgressBar(count($existingPostIds));
        $this->bar->setFormat('custom');
        $this->bar->setMessage('Updating existing posts...');

        foreach ($existingPostIds as $postsId) {
            $apiPostData = (new RedditClient())->getComments($postsId, $accessToken);
            $this->prepareParse($apiPostData, false);
            $this->bar->advance();
        }
    }

    public function prepareParse($postData, $archive)
    {
        $jsonPost = $postData[0]->data->children[0]->data;

        //check if post already exists
        $tmpPost = Tmppost::where('id', '=', $jsonPost->id)->first();
        $post = Post::where('id', '=', $jsonPost->id)->first();

        $age = time() - $jsonPost->created_utc;

        if ($post === null && $tmpPost === null) {
            $this->parsePost($postData);

            return;
        }

        if (!$archive) { // update post if it already exists in the database (only in non-archive mode)
            $postToUpdate = Post::whereId($jsonPost->id)->first();
            if (!$postToUpdate) {
                throw new PostNotFoundException('No message found to update');
            }
            $postToUpdate->updatePost($jsonPost, $this->bar);
        }
    }

    private function parsePost($postData)
    {
        $jsonPost = $postData[0]->data->children[0]->data;

        /* check for characteristic characters from the already established format
        Player Name | Song Artist - Song Title [Diff Name] +Mods */
        $postTitle = $jsonPost->title;
        if (!preg_match(self::REGEX_SCOREPOST, $postTitle)) {
            return false;
        }

        //clean up post title from various annotations
        $postTitle = preg_replace(self::REGEX_POST_TITLE, '', $postTitle);
        foreach (self::TITLE_IGNORES as $ignore) {
            $postTitle = preg_replace("/([\[\(]\Q" . $ignore . "\E[\]\)])/i", '', $postTitle);
        }
        //parse relevant information from post title
        $parsedPost = new Post();

        $matches = [];
        //Player
        $match = preg_match(self::REGEX_POST_PLAYER_NAME, $postTitle, $matches);
        if ($match === false || count($matches) !== 2) {
            Tmppost::addTmpPost($jsonPost);

            return false;
        }
        $playerName = trim($matches[1]);

        //map Data
        $match = preg_match(self::REGEX_POST_MAP_DATA, $postTitle, $matches);
        if ($match === false || count($matches) !== 2) {
            Tmppost::addTmpPost($jsonPost);

            return false;
        }

        $tmpMap = $matches[1];

        //split map Data
        $match = preg_match(self::REGEX_POST_MAP_DATA_GROUPS, $tmpMap, $matches);
        if ($match === false || count($matches) !== 4) {
            Tmppost::addTmpPost($jsonPost);

            return false;
        }

        $parsedPost->map_artist = htmlspecialchars_decode(trim($matches[1]));
        $parsedPost->map_title = htmlspecialchars_decode(trim($matches[2]));
        $parsedPost->map_diff = htmlspecialchars_decode(trim($matches[3]));

        //check some additional stuff before marking as final
        $this->bar->setMessage('Parsed: ' . $playerName . ' | ' .
            $parsedPost->map_artist . ' - ' .
            $parsedPost->map_title . ' [' .
            $parsedPost->map_diff . "]");

        $jsonPostDetail = $postData[0]->data->children[0]->data;
        if (!$jsonPostDetail) {
            return false;
        }
        $this->preparePost($jsonPostDetail, $parsedPost, $playerName);

        return true;
    }

    private function preparePost($jsonPost, Post $parsedPost, $playerName)
    {
        $apiPlayer = $this->osuClient->getUser($playerName);

        //if the api can find the username, check if its in the DB and insert
        if ($apiPlayer->getId()) {
            $dbPlayer = Player::where('id', '=', $apiPlayer->getId())->first();
            if ($dbPlayer === null) {
                $dbPlayer = Player::createPlayer($apiPlayer);
            }

            $this->updatePlayer($apiPlayer, $dbPlayer);
            $parsedPost->addPost($jsonPost, $apiPlayer);
        } else {
            /* else check if the username exists in the DB as an alias and retry
               the osu!api call using the alias */
            $alias = Alias::where('alias', '=', $playerName)->orderBy('created_at', 'DESC')->first();
            if ($alias !== null) {
                $userByAlias = $this->osuClient->getUser(urlencode($alias->alias));
                if ($userByAlias !== null) {
                    $parsedPost->addPost($jsonPost, $userByAlias);
                }
            } else {
                Tmppost::addTmpPost($jsonPost);
            }
        }
    }

    // Update the player's name in the db if it has changed
    private function updatePlayer(User $apiPlayer, Player $dbPlayer)
    {
        if (
            $dbPlayer->name !== ''
            && $dbPlayer->name !== null
            && $dbPlayer->name !== $apiPlayer->getName()
        ) {
            $alias = Alias::createAlias($apiPlayer->getId(), $dbPlayer->name);

            $dbPlayer->name = $apiPlayer->getName();
            if ($dbPlayer->save()) {
                $this->bar->setMessage("Player \"" . $alias->alias . "\" updated successfully! New name:" . $dbPlayer->name);
            }
        }
    }
}
