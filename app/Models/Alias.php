<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Alias
 *
 * @property int $id
 * @property int $player_id
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|Alias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alias query()
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alias whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Alias extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        return $this->belongsTo('App\Models\Player', 'player_id', 'id');
    }

    /**
     * @param int $playerId
     * @param string $name
     *
     * @return Alias
     */
    public static function createAlias(int $playerId, string $name): Alias
    {
        $existingAlias = self::whereAlias($name)->where('player_id', '!=', $playerId);
        if ($existingAlias) {
            $existingAlias->delete();
        }

        $alias = new self();
        $alias->player_id = $playerId;
        $alias->alias = $name;
        $alias->save();

        return $alias;
    }
}
