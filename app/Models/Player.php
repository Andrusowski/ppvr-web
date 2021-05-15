<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{

    public function post()
    {
        return $this->hasMany('App\Models\Post', 'player_id', 'id');
    }
}
