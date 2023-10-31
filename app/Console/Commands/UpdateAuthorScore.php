<?php

namespace App\Console\Commands;

use App\Services\ScoreService;
use Illuminate\Console\Command;

class UpdateAuthorScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:author:score {author_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update authors\' scores';

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
        ScoreService::updateAuthorScore($this->argument('author_name'), $this->output);
        $this->newLine();
    }
}
