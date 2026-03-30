<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserGameStats
 *
 * @property int $id
 * @property int $user_id
 * @property int $games_played
 * @property int $total_correct_rounds
 * @property int $current_streak
 * @property int $max_streak
 * @property array|null $round_breakdown
 * @property \Illuminate\Support\Carbon|null $last_played_date
 * @property int|null $last_played_round
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserGameStats newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGameStats newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGameStats query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGameStats whereUserId($value)
 * @mixin \Eloquent
 */
class UserGameStats extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_game_stats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'games_played',
        'total_correct_rounds',
        'current_streak',
        'max_streak',
        'round_breakdown',
        'last_played_date',
        'last_played_round_results',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'round_breakdown' => 'array',
        'last_played_round_results' => 'array',
        'last_played_date' => 'date',
    ];

    /**
     * Get the user that owns the stats.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the user has already played today's game.
     */
    public function hasPlayedToday(): bool
    {
        if ($this->last_played_date === null) {
            return false;
        }

        return $this->last_played_date->isSameDay(Carbon::today('UTC'));
    }

    /**
     * Get today's game info if played.
     *
     * @return array<string, mixed>|null
     */
    public function getTodaysGame(): ?array
    {
        if (!$this->hasPlayedToday()) {
            return null;
        }

        $results = $this->last_played_round_results ?? [];
        $correctCount = count(array_filter($results, function ($r) {
            return $r === true;
        }));

        return [
            'round' => count($results),
            'correctCount' => $correctCount,
            'roundResults' => $results,
        ];
    }

    /**
     * Record that the user played today's game.
     * Also updates the streak based on whether the user played yesterday.
     *
     * @param array<int, bool|null> $roundResults
     */
    public function recordGamePlayed(array $roundResults): void
    {
        $this->last_played_round_results = $roundResults;
    }

    /**
     * Get default stats structure.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultStats(): array
    {
        return [
            'gamesPlayed' => 0,
            'totalCorrectRounds' => 0,
            'currentStreak' => 0,
            'maxStreak' => 0,
            'roundBreakdown' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'lastPlayedDate' => null,
        ];
    }

    /**
     * Convert model to frontend format.
     *
     * @return array<string, mixed>
     */
    public function toFrontendFormat(): array
    {
        return [
            'gamesPlayed' => $this->games_played,
            'totalCorrectRounds' => $this->total_correct_rounds,
            'currentStreak' => $this->current_streak,
            'maxStreak' => $this->max_streak,
            'roundBreakdown' => $this->round_breakdown ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'lastPlayedDate' => $this->last_played_date?->format('Y-m-d'),
        ];
    }

    /**
     * Update stats from frontend format.
     *
     * @param array<string, mixed> $frontendStats
     */
    public function updateFromFrontendFormat(array $frontendStats): void
    {
        $this->games_played = $frontendStats['gamesPlayed'] ?? 0;
        $this->total_correct_rounds = $frontendStats['totalCorrectRounds'] ?? 0;
        $this->current_streak = $frontendStats['currentStreak'] ?? 0;
        $this->max_streak = $frontendStats['maxStreak'] ?? 0;
        $this->round_breakdown = $frontendStats['roundBreakdown'] ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        if (array_key_exists('lastPlayedDate', $frontendStats)) {
            $this->last_played_date = $frontendStats['lastPlayedDate'];
        }
    }
}
