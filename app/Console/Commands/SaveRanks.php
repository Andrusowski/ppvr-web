<?php

namespace App\Console\Commands;

use App\Player;
use App\Rank;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        $players = Player::orderBy('score', 'DESC')->get();
        $bar = $this->output->createProgressBar($players->count());
        DB::beginTransaction();

        foreach ($players as $rankNr => $player) {
            $rank = new Rank();
            $rank->player_id = $player->id;
            $rank->rank = ($rankNr + 1);
            if ($this->argument('timestamp') != null) {
                $rank->created_at = $this->argument('timestamp');
                $rank->updated_at = $this->argument('timestamp');
            }
            $rank->save();

            $rankNr++;
            $bar->advance();
        }

        $month_seconds = 2629743;
        $timestamp = time() - ($month_seconds * 3);
        DB::table('ranks')->where('created_at', '<', gmdate("Y-m-d", $timestamp))->delete();

        DB::commit();
        $bar->finish();
        $this->line();
    }
}
