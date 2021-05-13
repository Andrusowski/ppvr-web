<?php

namespace App\Console\Commands;

use App\Alias;
use App\Console\Requests\OsuRequest;
use App\Player;
use Illuminate\Console\Command;

class GetAliases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:aliases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse players\' aliases from their profile pages';

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
        stream_context_set_default(
            ['http' => [
                'ignore_errors' => true],
            ]
        );

        $players = Player::all();

        $bar = $this->output->createProgressBar(count($players));

        $osuRequest = new OsuRequest();
        foreach ($players as $player) {
            $playerPage = $osuRequest->getPlayerPage($player->id);

            $matches = [];
            preg_match('/previous_usernames":\[\"(.*)\"\]/m', $playerPage, $matches);

            if (count($matches) > 0) {
                $aliases = explode('","', $matches[1]);
                foreach ($aliases as $alias) {
                    $aliasEntity = new Alias();
                    $aliasEntity->player_id = $player->id;
                    $aliasEntity->alias = $alias;

                    if ($aliasEntity->save()) {
                        $this->info($player->name . '->' . $alias);
                    }
                }
            }

            $bar->advance();
            usleep(100000);
        }

        $bar->finish();
    }
}
