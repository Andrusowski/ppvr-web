<?php
/**
 * Copyright (c) basecom GmbH & Co. KG
 * Licensed under the MIT License
 */

namespace App\Services\Controller;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatsControllerService
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getPostsHistory(): Collection
    {
        return DB::table('posts')
                 ->select(DB::raw('COUNT(id) as postsDaily'), DB::raw('DATE(created_at) as date'))
                 ->groupBy('date')
                 ->orderBy('date', 'DESC')
                 ->limit(90)
                 ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getUpvotesHistory(): Collection
    {
        return DB::table('posts')
                 ->select(DB::raw('SUM(score) as postsDaily'), DB::raw('DATE(created_at) as date'))
                 ->groupBy('date')
                 ->orderBy('date', 'DESC')
                 ->limit(90)
                 ->get();
    }
}
