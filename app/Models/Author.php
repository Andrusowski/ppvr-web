<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Authors
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Author newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Author newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Author query()
 * @property int $id
 * @property string $name
 * @property int $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCreatedAt( $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereId( $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereName( $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereScore( $value)
 * @method static \Illuminate\Database\Eloquent\Builder|Author whereUpdatedAt( $value)
 * @mixin \Eloquent
 */
class Author extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
