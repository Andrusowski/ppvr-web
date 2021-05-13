<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tmppost extends Model
{
    protected $casts = [
      'id' => 'string',
    ];
}
