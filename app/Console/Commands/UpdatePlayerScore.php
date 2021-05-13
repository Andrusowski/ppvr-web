<?php

namespace App\Console\Commands;

use App\Player;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePlayerScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:score {--a} {player_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update players\' score';

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
        $playerIds = [];
        if ($this->hasOption('a')) {
            $playerCollection = Player::all();
            foreach ($playerCollection as $player) {
                $playerIds[] = $player->id;
            }
        } elseif ($this->hasArgument('player_id')) {
            $playerId[] = $this->argument('player_id');
        }

        $bar = $this->output->createProgressBar(count($playerIds));
        DB::beginTransaction();

        foreach ($playerIds as $playerId) {
            DB::statement('
            UPDATE players
            JOIN (
                SELECT player_id, SUM(round((score+(platinum*180)+(gold*50)+(silver*10)) * POWER(0.95, row_num - 1))) AS weighted
                FROM (
                    SELECT row_number() over (partition BY player_id ORDER BY score DESC) row_num, score, silver, gold, platinum, player_id
                    FROM posts
                    ORDER BY score DESC
                ) AS ranking
                GROUP BY player_id
                ORDER BY weighted DESC
            ) weighted ON players.id=weighted.player_id
            SET score=weighted.weighted
            WHERE players.id=' . $playerId);

            $bar->advance();
        }

        DB::commit();
        $bar->finish();
    }
}
