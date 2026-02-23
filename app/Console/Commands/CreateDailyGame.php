<?php

namespace App\Console\Commands;

use App\Services\Controller\GameControllerService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateDailyGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:create-daily {--date= : The date to create game for (YYYY-MM-DD, defaults to today UTC)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the daily game for today (or specified date). Should run at midnight UTC.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $date = $this->option('date');

        if ($date) {
            try {
                $gameDate = Carbon::parse($date)->toDateString();
            } catch (\Exception $e) {
                $this->error('Invalid date format. Use YYYY-MM-DD.');

                return Command::FAILURE;
            }
        } else {
            $gameDate = Carbon::now('UTC')->toDateString();
        }

        $this->info("Creating daily game for {$gameDate}...");

        $service = new GameControllerService();
        $result = $service->createDailyGameForDate($gameDate);

        if ($result['created']) {
            $this->info("Daily game created successfully with " . count($result['game']->post_ids) . " posts.");

            return Command::SUCCESS;
        }

        $this->warn("Daily game for {$gameDate} already exists.");

        return Command::SUCCESS;
    }
}
