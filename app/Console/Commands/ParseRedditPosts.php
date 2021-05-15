<?php

namespace App\Console\Commands;

use App\Models\Alias;
use App\Services\Clients\OsuClient;
use App\Services\Clients\RedditClient;
use App\Models\Player;
use App\Models\Post;
use App\Models\Tmppost;
use App\Services\RedditParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ParseRedditPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:reddit {--archive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse new Reddit scoreposts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param RedditParser $redditParser
     *
     * @return mixed
     */
    public function handle(RedditParser $redditParser)
    {
        $archiveFirstPost = $this->option('archive');

        if ($archiveFirstPost) {
            $redditParser->archive();
        } else {
            $redditParser->new();
        }

        return static::SUCCESS;
    }
}
