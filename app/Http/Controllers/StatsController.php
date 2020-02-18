<?php

namespace App\Http\Controllers;

class StatsController extends Controller
{
    # Simple HTML page for now. Move changelog into the DB later when it gets huge.
    public function getIndex()
    {
        return view('content.stats');
    }
}