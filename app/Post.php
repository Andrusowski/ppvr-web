<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $casts = [
      'id' => 'string'
    ];

    public static $rules = [
    	'player' => 'required|max:15',
    	'artist' => 'required',
        'title' => 'required',
        'diff' => 'required'
    ];

    public function player()
    {
      return $this->belongsTo('App\Player', 'player_id', 'id');
    }
}
