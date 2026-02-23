<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DailyGame
 *
 * @property int $id
 * @property string $game_date
 * @property array $post_ids
 * @property array|null $reddit_data
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DailyGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|DailyGame whereGameDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyGame wherePostIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DailyGame whereRedditData($value)
 * @mixin \Eloquent
 */
class DailyGame extends Model
{
    protected $fillable = [
        'game_date',
        'post_ids',
        'reddit_data',
    ];

    protected $casts = [
        'post_ids' => 'array',
        'reddit_data' => 'array',
        'game_date' => 'date',
    ];

    /**
     * Get the posts for this daily game.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPosts()
    {
        return Post::whereIn('id', $this->post_ids)->get()->keyBy('id');
    }
}
