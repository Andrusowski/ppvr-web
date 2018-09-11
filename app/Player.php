<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{    
    public function post()
    {
        return $this->hasMany('App\Post', 'player_id', 'id');
    }
}
