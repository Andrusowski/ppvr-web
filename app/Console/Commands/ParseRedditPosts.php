<?php

namespace App\Console\Commands;

use App\Services\RedditParser;
use DateTime;
use Illuminate\Console\Command;

class ParseRedditPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:reddit {--single= } {--archive} {--archive-top} {--archive-users} {--sort=} {--top-by=} {--update} {--update-min-score=} {--update-min-time=}';

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
        $singlePostId = $this->option('single');

        $archiveFirstPost = $this->option('archive');
        $sort = $this->option('sort');
        $topBy = $this->option('top-by');

        $update = $this->option('update');
        $updateMinScore = (int)$this->option('update-min-score');
        $minTimeRaw = $this->option('update-min-time');
        $minTime = $minTimeRaw ? new DateTime('@' . strtotime($minTimeRaw)) : null;

        if ($singlePostId) {
            $redditParser->single($singlePostId);
        } elseif ($archiveFirstPost) {
            $redditParser->archive();
        } elseif ($this->option('archive-users')) {
            $redditParser->archiveTopUsers($sort, $topBy);
        } elseif ($this->option('archive-top')) {
            $redditParser->archiveTop($topBy);
        } elseif ($update) {
            $redditParser->updateFromTop($updateMinScore, $minTime);
        } else {
            $redditParser->new();
        }

        $this->newLine();

        return static::SUCCESS;
    }
}
