<?php

namespace App\Models;

use App\Models\Api\User;
use App\Services\ScoreService;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Helper\ProgressBar;
use function Sentry\captureMessage;

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

    public function addPost($jsonPost, User $user)
    {
        $this->id = $jsonPost->id;
        $this->player_id = $user->getId();
        $this->author = $jsonPost->author;
        $this->ups = round($jsonPost->score * $jsonPost->upvote_ratio);
        $this->downs = round($jsonPost->score * (1 - $jsonPost->upvote_ratio));
        $this->score = $jsonPost->score;
        $this->final = 0; // deprecated, can be removed in future versions
        $this->created_utc = $jsonPost->created_utc;

        //post platin and silver update
        if (isset($jsonPost->gildings)) {
            $this->setAwards($jsonPost);
        } else {
            $this->gold = $jsonPost->gilded;
        }

        if ($this->save()) {
            $this->updateScores();
        } else {
            captureMessage('Could not save post ' . $this->id);
        }
    }

    public function updatePost($jsonPost, ProgressBar $bar)
    {
        $scorePre = $this->score;

        $this->ups = round($jsonPost->score * $jsonPost->upvote_ratio);
        $this->downs = round($jsonPost->score * (1 - $jsonPost->upvote_ratio));
        $this->score = $jsonPost->score;

        // update if needed
        if ($scorePre !== $this->score) {
            $this->final = 0;

            //post platin and silver update
            if (isset($jsonPost->gildings)) {
                $this->setAwards($jsonPost);
            } else {
                $this->gold = $jsonPost->gilded;
            }

            if ($this->save()) {
                $this->updateScores();
                $bar->setMessage('Updated: ' .
                    $this->map_artist . ' - ' .
                    $this->map_title . ' [' .
                    $this->map_diff . "] " .
                    'Score: ' . $scorePre . ' -> ' . $this->score);
            }
        }
    }

    private function setAwards($jsonPost)
    {
        if (isset($jsonPost->gildings->gid_1)) {
            $this->silver = $jsonPost->gildings->gid_1;
        } else {
            $this->silver = 0;
        }

        if (isset($jsonPost->gildings->gid_2)) {
            $this->gold = $jsonPost->gildings->gid_2;
        } else {
            $this->gold = 0;
        }

        if (isset($jsonPost->gildings->gid_3)) {
            $this->platinum = $jsonPost->gildings->gid_3;
        } else {
            $this->platinum = 0;
        }
    }

    private function updateScores()
    {
        ScoreService::updatePlayerScore($this->player_id);
        ScoreService::updateAuthorScore($this->author);
    }
}
