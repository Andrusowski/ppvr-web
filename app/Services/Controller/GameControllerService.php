<?php

namespace App\Services\Controller;

use App\Models\DailyGame;
use App\Models\Player;
use App\Models\Post;
use App\Services\Clients\RedditClient;
use Carbon\Carbon;

class GameControllerService
{
    public const POSTS_PER_GAME = 11;
    public const ROUNDS_PER_GAME = 10;
    public const MIN_SCORE = 100;

    private RedditClient $redditClient;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->redditClient = new RedditClient();
    }

    /**
     * Get the daily game for today (UTC). Returns null if not created yet.
     *
     * @return DailyGame|null
     */
    public function getDailyGame(): ?DailyGame
    {
        $today = Carbon::now('UTC')->toDateString();

        return DailyGame::whereGameDate($today)->first();
    }

    /**
     * Create a daily game for a specific date. Used by the scheduled command.
     *
     * @param string $date
     *
     * @return array{created: bool, game: DailyGame}
     */
    public function createDailyGameForDate(string $date): array
    {
        $existingGame = DailyGame::whereGameDate($date)->first();

        if ($existingGame) {
            return ['created' => false, 'game' => $existingGame];
        }

        $game = $this->createDailyGame($date);

        return ['created' => true, 'game' => $game];
    }

    /**
     * Get or create the daily game for today.
     * @deprecated Use getDailyGame() instead. Games should be created by scheduled command.
     *
     * @return DailyGame
     */
    public function getOrCreateDailyGame(): DailyGame
    {
        $today = Carbon::now('UTC')->toDateString();

        $game = DailyGame::whereGameDate($today)->first();

        if (!$game) {
            $game = $this->createDailyGame($today);
        }

        return $game;
    }

    /**
     * Create a new daily game with randomly selected posts.
     * Posts are selected with bias towards higher scores.
     * Reddit data (titles, top comments) is fetched and persisted.
     *
     * @param string $date
     *
     * @return DailyGame
     */
    private function createDailyGame(string $date): DailyGame
    {
        $postIds = $this->selectRandomPosts();
        $redditData = $this->fetchRedditData($postIds);

        $game = new DailyGame();
        $game->game_date = $date;
        $game->post_ids = $postIds;
        $game->reddit_data = $redditData;
        $game->save();

        return $game;
    }

    /**
     * Select random posts with bias towards higher scores.
     * Uses weighted random selection based on score buckets.
     *
     * @return array
     */
    private function selectRandomPosts(): array
    {
        // Get posts grouped by score ranges with weighted selection
        // Higher scores have more weight (appear more often)
        $posts = collect();

        $highScorePosts = Post::where('score', '>=', 1000)
            ->inRandomOrder()
            ->limit(7)
            ->pluck('id');
        $posts = $posts->merge($highScorePosts);

        $midScorePosts = Post::where('score', '>=', static::MIN_SCORE)
            ->where('score', '<', 1000)
            ->inRandomOrder()
            ->limit(5)
            ->pluck('id');
        $posts = $posts->merge($midScorePosts);

        $selectedPosts = $posts->shuffle()->take(static::POSTS_PER_GAME);

        return $selectedPosts->values()->toArray();
    }

    /**
     * Get game data for the frontend, including posts with their details.
     * Reddit data (titles, top comments) is retrieved from the persisted data.
     *
     * @param DailyGame $game
     *
     * @return array
     */
    public function getGameData(DailyGame $game): array
    {
        $posts = [];
        $redditData = $game->reddit_data ?? [];

        foreach ($game->post_ids as $postId) {
            $post = Post::find($postId);
            if (!$post) {
                continue;
            }

            $player = Player::find($post->player_id);
            $playerName = $player ? $player->name : 'Unknown';
            $postRedditData = $redditData[$post->id] ?? [];

            $posts[] = [
                'id' => $post->id,
                'title' => $playerName . ' | ' . $post->map_artist . ' - ' . $post->map_title . ' [' . $post->map_diff . ']',
                'player_name' => $playerName,
                'map_artist' => $post->map_artist,
                'map_title' => $post->map_title,
                'map_diff' => $post->map_diff,
                'author' => $post->author,
                'score' => $post->score,
                'created_at' => $post->created_utc,
                'reddit_title' => $postRedditData['title'] ?? null,
                'top_comment' => $postRedditData['top_comment'] ?? null,
                'top_comment_author' => $postRedditData['top_comment_author'] ?? null,
            ];
        }

        return [
            'date' => $game->game_date instanceof Carbon ? $game->game_date->toDateString() : $game->game_date,
            'posts' => $posts,
            'total_rounds' => static::ROUNDS_PER_GAME,
        ];
    }

    /**
     * Fetch Reddit data (title and top comment) for a list of post IDs.
     *
     * @param array $postIds
     *
     * @return array Associative array mapping post ID to Reddit data
     */
    private function fetchRedditData(array $postIds): array
    {
        $redditData = [];

        foreach ($postIds as $postId) {
            $redditData[$postId] = $this->fetchPostRedditData($postId);
        }

        return $redditData;
    }

    /**
     * Fetch Reddit data (title and top comment) for a single post.
     * Filters out comments by "osu-bot".
     *
     * @param string $postId
     *
     * @return array
     */
    private function fetchPostRedditData(string $postId): array
    {
        try {
            if ($this->accessToken === null) {
                $this->accessToken = $this->redditClient->getAccessToken();
            }

            $postData = $this->redditClient->getPostWithComments($postId, $this->accessToken);

            $result = [
                'title' => null,
                'top_comment' => null,
                'top_comment_author' => null,
            ];

            if ($postData && isset($postData->post->title)) {
                $result['title'] = $postData->post->title;
            }

            // Find the top comment that is NOT from "osu-bot" or "[deleted]"
            if ($postData && !empty($postData->comments)) {
                foreach ($postData->comments as $comment) {
                    if (!isset($comment->data)) {
                        continue;
                    }

                    $commentData = $comment->data;

                    // Skip if author is "osu-bot" or "[deleted]"
                    if (isset($commentData->author) && (strtolower($commentData->author) === 'osu-bot' || strtolower($commentData->author) === '[deleted]')) {
                        continue;
                    }

                    // Use this comment as the top comment
                    if (isset($commentData->body)) {
                        $result['top_comment'] = $commentData->body;
                        $result['top_comment_author'] = $commentData->author ?? null;

                        break;
                    }
                }
            }

            return $result;
        } catch (\Throwable $e) {
            // Silently fail and return empty data
        }

        return [
            'title' => null,
            'top_comment' => null,
            'top_comment_author' => null,
        ];
    }

    /**
     * Validate a player's choice for a round.
     *
     * @param DailyGame $game
     * @param int $round
     * @param string $chosenPostId
     *
     * @return array
     */
    public function validateChoice(DailyGame $game, int $round, string $chosenPostId): array
    {
        $postIds = $game->post_ids;

        // Round 1: compare posts[0] and posts[1]
        // Round N: compare posts[N-1] and posts[N]
        $leftIndex = $round - 1;
        $rightIndex = $round;

        if ($rightIndex >= count($postIds)) {
            return [
                'valid' => false,
                'error' => 'Invalid round',
            ];
        }

        $leftPost = Post::find($postIds[$leftIndex]);
        $rightPost = Post::find($postIds[$rightIndex]);

        if (!$leftPost || !$rightPost) {
            return [
                'valid' => false,
                'error' => 'Posts not found',
            ];
        }

        // Determine the correct answer (the one with higher score)
        $correctPostId = $leftPost->score >= $rightPost->score ? $leftPost->id : $rightPost->id;
        $isCorrect = $chosenPostId === $correctPostId;

        return [
            'valid' => true,
            'correct' => $isCorrect,
            'left_score' => $leftPost->score,
            'right_score' => $rightPost->score,
            'correct_post_id' => $correctPostId,
        ];
    }
}
