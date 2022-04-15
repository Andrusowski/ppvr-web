<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Post
 *
 * @property string $id
 * @property int $player_id
 * @property string $map_artist
 * @property string $map_title
 * @property string $map_diff
 * @property string $author
 * @property int $score
 * @property int $ups
 * @property int $downs
 * @property int $silver
 * @property int $gold
 * @property int $platinum
 * @property int $created_utc
 * @property int $final
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedUtc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDowns($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereFinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereGold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMapArtist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMapDiff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMapTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePlatinum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSilver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUps($value)
 * @mixin \Eloquent
 */
class Post extends Model
{
    protected $casts = [
      'id' => 'string',
    ];

    public static $rules = [
        'player' => 'required|max:15',
        'artist' => 'required',
        'title' => 'required',
        'diff' => 'required',
    ];

    public function player()
    {
        return $this->belongsTo('App\Models\Player', 'player_id', 'id');
    }
}
