<?php

namespace App\Console\Commands;

use App\Services\RedditParser;
use Illuminate\Console\Command;

class ParseRedditPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:reddit {--archive} {--update} {--update-min-score=}';

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
        $update = $this->option('update');
        $updateMinScore = (int)$this->option('update-min-score');

        if ($archiveFirstPost) {
            $redditParser->archive();
        } elseif ($update) {
            $redditParser->updateFromTop($updateMinScore);
        } else {
            $redditParser->new();
        }

        $this->newLine();

        return static::SUCCESS;
    }
}
