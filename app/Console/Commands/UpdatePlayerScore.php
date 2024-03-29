<?php

namespace App\Console\Commands;

use App\Services\ScoreService;
use Illuminate\Console\Command;

class UpdatePlayerScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:player:score {player_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update players\' scores';

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
        ScoreService::updatePlayerScore($this->argument('player_id'), $this->output);
        $this->newLine();
    }
}
