<?php

namespace App\Console\Commands;

use App\Services\ScoreService;
use Illuminate\Console\Command;

class SaveRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:ranks {timestamp?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves Player\'s Ranks in the ranks database-table';

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
     * @return mixed
     */
    public function handle()
    {
        ScoreService::savePlayerRanks($this->argument('timestamp'), $this->output);
        $this->newLine();
    }
}
