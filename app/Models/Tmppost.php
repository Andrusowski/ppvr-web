<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tmppost
 *
 * @property string $id
 * @property string $title
 * @property string $author
 * @property int $score
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tmppost whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tmppost extends Model
{
    protected $casts = [
      'id' => 'string',
    ];

    public static function addTmpPost($jsonPost)
    {
        $post = new self();

        $post->id = $jsonPost->id;
        $post->title = $jsonPost->title;
        $post->author = $jsonPost->author;

        //$ups = round($jsonPost->score * $jsonPost->upvote_ratio);
        //$downs = round($jsonPost->score * (1 - $jsonPost->upvote_ratio));
        $post->score = $jsonPost->score;

        $post->save();
    }
}
